<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\BiayaOperasional;
use Illuminate\Http\Request;
use Carbon\Carbon; 
use App\Notifications\TicketAssigned;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $ticket = Ticket::with(['pelapor', 'teknisi', 'biayaOperasional.staff', 'biayaOperasional.pemberi'])
            ->findOrFail($id);

        // Ambil semua user dengan role Staff, lalu hitung tiket mereka hari ini
        $staffList = User::where('role', 'Staff')->get()->map(function($staff) {
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
        // TRIGGER NOTIFIKASI MULTI-CHANNEL OPTIMAL
        // ========================================================
        $teknisi = User::find($request->teknisi_id);
        if ($teknisi) {
            // Menggunakan fresh() agar instance tiket memperbarui isi data terbarunya setelah di-update
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
     * Simpan Biaya Operasional / Bonus untuk Staff yang menyelesaikan tiket (dari detail tiket)
     */
    public function tambahBiaya(Request $request, $id)
    {
        $ticket = Ticket::with('teknisi')->findOrFail($id);

        // Hanya boleh ditambahkan jika tiket sudah Closed
        if ($ticket->status !== 'Closed') {
            return back()->with('error', 'Biaya operasional hanya dapat ditambahkan untuk tiket yang sudah Closed.');
        }

        // Harus ada teknisi yang mengerjakan
        if (!$ticket->teknisi_id) {
            return back()->with('error', 'Tiket ini belum memiliki teknisi yang mengerjakan.');
        }

        $request->validate([
            'nominal'    => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'nominal.required' => 'Nominal biaya operasional wajib diisi.',
            'nominal.min'      => 'Nominal minimum adalah Rp 1.',
        ]);

        // Cek apakah sudah ada (update) atau belum (create)
        BiayaOperasional::updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'staff_id'          => $ticket->teknisi_id,
                'diberikan_oleh'    => Auth::id(),
                'nominal'           => $request->nominal,
                'keterangan'        => $request->keterangan,
                'tanggal_pemberian' => now()->toDateString(),
            ]
        );

        return back()->with('success', 'Biaya operasional sebesar Rp ' . number_format($request->nominal, 0, ',', '.') . ' berhasil disimpan untuk ' . ($ticket->teknisi->nama ?? 'Staff') . '!');
    }

    /**
     * Simpan Biaya Operasional secara mandiri/bebas (dari form rekap biaya)
     */
    public function storeBiaya(Request $request)
    {
        $request->validate([
            'tanggal_pemberian' => 'required|date',
            'staff_id'          => 'required|exists:users,id',
            'nominal'           => 'required|numeric|min:1',
            'keterangan'        => 'nullable|string|max:500',
            'ticket_id'         => 'nullable|exists:tickets,id',
        ], [
            'tanggal_pemberian.required' => 'Tanggal pemberian wajib diisi.',
            'staff_id.required'          => 'Pilih staff/teknisi penerima biaya.',
            'nominal.required'           => 'Nominal biaya operasional wajib diisi.',
            'nominal.min'                => 'Nominal minimum adalah Rp 1.',
        ]);

        BiayaOperasional::create([
            'tanggal_pemberian' => $request->tanggal_pemberian,
            'staff_id'          => $request->staff_id,
            'nominal'           => $request->nominal,
            'keterangan'        => $request->keterangan,
            'ticket_id'         => $request->ticket_id ?? null,
            'diberikan_oleh'    => Auth::id(),
        ]);

        return back()->with('success', 'Biaya operasional sebesar Rp ' . number_format($request->nominal, 0, ',', '.') . ' berhasil ditambahkan!');
    }

    /**
     * Hapus Biaya Operasional
     */
    public function hapusBiaya($id)
    {
        $biaya = BiayaOperasional::findOrFail($id);
        $biaya->delete();

        return back()->with('success', 'Biaya operasional berhasil dihapus.');
    }

    /**
     * Halaman Rekap Biaya Operasional per Staff
     */
    public function rekapBiaya(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('m'));
        $tahun = $request->get('tahun', now()->format('Y'));
        $staffId = $request->get('staff_id');

        // Query data biaya operasional
        $query = BiayaOperasional::with(['staff', 'ticket', 'pemberi'])
            ->whereYear('tanggal_pemberian', $tahun)
            ->whereMonth('tanggal_pemberian', $bulan);

        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        $dataRekap = $query->latest()->get();

        // Rekap per staff: group by staff
        $rekapPerStaff = $dataRekap->groupBy('staff_id')->map(function ($items) {
            return [
                'staff'         => $items->first()->staff,
                'jumlah_tiket'  => $items->count(),
                'total_nominal' => $items->sum('nominal'),
                'items'         => $items,
            ];
        })->sortByDesc('total_nominal');

        // Total keseluruhan
        $grandTotal = $dataRekap->sum('nominal');

        // Daftar staff untuk filter
        $staffList = User::where('role', 'Staff')->orderBy('nama')->get();

        return view('admin.helpdesk.rekap_biaya', [
            'title'         => 'Rekap Biaya Operasional Staff',
            'rekapPerStaff' => $rekapPerStaff,
            'dataRekap'     => $dataRekap,
            'grandTotal'    => $grandTotal,
            'staffList'     => $staffList,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'staffId'       => $staffId,
        ]);
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
            'title'         => 'Laporan Kepuasan Pengguna',
            'tickets'       => $tickets,
            'startDate'     => $startDate,
            'endDate'       => $endDate,
            'totalFeedback' => $totalFeedback,
            'averageRating' => $averageRating
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
            'tickets'       => $tickets,
            'startDate'     => $startDate,
            'endDate'       => $endDate,
            'totalFeedback' => $totalFeedback,
            'averageRating' => $averageRating
        ]);
    }

    /**
     * Cetak Laporan Rekapitulasi Biaya Operasional ke PDF
     */
    public function cetakBiayaOperasional(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('m'));
        $tahun = $request->get('tahun', now()->format('Y'));
        $staffId = $request->get('staff_id');

        // Query data biaya operasional
        $query = BiayaOperasional::with(['staff', 'ticket', 'pemberi'])
            ->whereYear('tanggal_pemberian', $tahun)
            ->whereMonth('tanggal_pemberian', $bulan);

        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        $dataRekap = $query->latest()->get();
        $grandTotal = $dataRekap->sum('nominal');

        // Format nama bulan untuk judul dokumen (Menggunakan Carbon diterjemahkan ke Bahasa Indonesia jika locale diset id)
        $namaBulan = Carbon::createFromFormat('m', $bulan)->translatedFormat('F');

        // Load view PDF
        $pdf = Pdf::loadView('admin.helpdesk.cetak_biaya_operasional', compact(
            'dataRekap', 'grandTotal', 'bulan', 'tahun', 'namaBulan'
        ))->setPaper('A4', 'landscape'); // Orientasi landscape disarankan agar tabel muat

        return $pdf->stream('Laporan_Biaya_Operasional_' . $namaBulan . '_' . $tahun . '.pdf');
    }
}