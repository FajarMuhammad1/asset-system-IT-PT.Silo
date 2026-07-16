<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon; 
use App\Notifications\TicketAssigned; // <-- Import class Notifikasi

class HelpdeskController extends Controller
{
    /**
     * Tampilkan semua tiket untuk monitoring admin
     */
    public function index()
    {
        // Mengurutkan tiket: Prioritas tertinggi (Urgent) di atas, lalu urutkan berdasarkan waktu terbaru
        $tickets = Ticket::with(['pelapor', 'teknisi'])
                         ->orderByRaw("FIELD(prioritas, 'Urgent', 'High', 'Normal', 'Low') DESC")
                         ->latest()
                         ->get();

        return view('admin.helpdesk.index', [
            'title'   => 'Monitoring Tiket Helpdesk',
            'tickets' => $tickets
        ]);
    }

    /**
     * Tampilkan detail tiket + form assign teknisi
     */
    public function show($id)
    {
        $ticket = Ticket::with(['pelapor', 'teknisi'])
                        ->findOrFail($id);

        // Ambil semua user dengan role Staff, lalu hitung tiket mereka hari ini
        $staffList = User::where('role', 'Staff')->get()->map(function($staff) {
            // Menghitung jumlah tiket yang dipegang staf ini pada hari ini
            $staff->task_count = Ticket::where('teknisi_id', $staff->id)
                                       ->whereDate('created_at', Carbon::today())
                                       ->count();
            return $staff;
        });

        return view('admin.helpdesk.show', [
            'title'     => 'Detail Tiket ' . $ticket->no_tiket,
            'ticket'    => $ticket, 
            'staffList' => $staffList
        ]);
    }

    /**
     * Proses assign teknisi (status tetap Open)
     */
    public function assignTeknisi(Request $request, $id)
    {
        $request->validate([
            'teknisi_id' => 'required|exists:users,id',
        ]);

        // ========================================================
        // VALIDASI MAX 5 TIKET PER HARI
        // ========================================================
        $jumlahTugasHariIni = Ticket::where('teknisi_id', $request->teknisi_id)
                                    ->whereDate('created_at', Carbon::today())
                                    ->count();

        if ($jumlahTugasHariIni >= 5) {
            return redirect()
                ->back()
                ->with('error', 'Gagal! Staf yang dipilih sudah mencapai batas maksimal 5 tiket hari ini.');
        }
        // ========================================================

        $ticket = Ticket::findOrFail($id);

        // Tidak merubah status (tetap Open)
        $ticket->update([
            'teknisi_id' => $request->teknisi_id,
        ]);

        // ========================================================
        // [PERBAIKAN] TRIGGER NOTIFIKASI MULTI-CHANNEL OPTIMAL
        // ========================================================
        $teknisi = User::find($request->teknisi_id);
        if ($teknisi) {
            // Menggunakan fresh() agar instance tiket memperbarui isi data terbarunya setelah di-update
            // Ini menjamin field baru seperti 'no_tiket', 'prioritas', dll. ikut terkirim ke WhatsApp/Telegram
            $ticketTerbaru = $ticket->fresh();
            
            // Eksekusi pengiriman notifikasi via App\Notifications\TicketAssigned
            $teknisi->notify(new TicketAssigned($ticketTerbaru));
        }

        return redirect()
            ->back()
            ->with('success', 'Teknisi berhasil diassign dan notifikasi (Web, Email, WA, Telegram) telah diproses!');
    }

    /**
     * Update Pengaturan Prioritas dan Tipe Penyelesaian Tiket
     */
    public function updateSettings(Request $request, $id)
    {
        // Validasi input sesuai dengan nilai yang ada di Database
        $request->validate([
            'prioritas'         => 'required|in:Low,Normal,High,Urgent',
            'tipe_penyelesaian' => 'required|in:Individu,Tim'
        ]);

        $ticket = Ticket::findOrFail($id);
        
        // Update data menggunakan mass assignment
        $ticket->update([
            'prioritas'         => $request->prioritas,
            'tipe_penyelesaian' => $request->tipe_penyelesaian,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Pengaturan Prioritas dan Tipe Pengerjaan tiket berhasil diperbarui!');
    }

    /**
     * Halaman Rekap Penilaian/Feedback Kepuasan Pengguna (Dilihat di Web Admin)
     */
    public function feedbackReport(Request $request)
    {
        // Default filter ke bulan berjalan
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Ambil tiket yang berstatus Closed dan SUDAH dinilai (punya feedback) berdasarkan rentang tanggal selesai
        $tickets = Ticket::with(['feedback', 'pelapor', 'teknisi'])
            ->whereHas('feedback')
            ->whereBetween('tgl_selesai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();

        // Perhitungan KPI Statistik Singkat
        $totalFeedback = $tickets->count();
        $averageRating = $tickets->avg(fn($t) => $t->feedback->rating) ?? 0;

        return view('admin.helpdesk.feedback_report', [
            'title'          => 'Laporan Kepuasan Pengguna',
            'tickets'        => $tickets,
            'startDate'      => $startDate,
            'endDate'        => $endDate,
            'totalFeedback'  => $totalFeedback,
            'averageRating'  => $averageRating
        ]);
    }

    /**
     * Halaman Khusus Print / Cetak Berkas Dokumen Kepuasan Pengguna
     */
    public function printFeedbackReport(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $tickets = Ticket::with(['feedback', 'pelapor', 'teknisi'])
            ->whereHas('feedback')
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('tgl_selesai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->latest()
            ->get();

        $totalFeedback = $tickets->count();
        $averageRating = $tickets->avg(fn($t) => $t->feedback->rating) ?? 0;

        return view('admin.helpdesk.feedback_print', [
            'tickets'        => $tickets,
            'startDate'      => $startDate,
            'endDate'         => $endDate,
            'totalFeedback'  => $totalFeedback,
            'averageRating'  => $averageRating
        ]);
    }
}