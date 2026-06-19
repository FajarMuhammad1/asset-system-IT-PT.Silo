<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TaskReport; // Ditambahkan agar bisa membuat data TaskReport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffHelpdeskController extends Controller
{
    /**
     * Halaman utama staff - daftar tiket yang ditugaskan (Individu & Kolaborasi Tim)
     */
    public function index()
    {
        $userId = Auth::id();

        // Mengambil tiket yang di-assign khusus ke staff ini
        // ATAU tiket yang bertipe 'Tim' dan statusnya belum selesai agar bisa dipantau bersama
        $tickets = Ticket::with(['pelapor', 'teknisi'])
            ->where(function($query) use ($userId) {
                $query->where('teknisi_id', $userId)
                      ->orWhere(function($subQuery) {
                          $subQuery->where('tipe_penyelesaian', 'Tim')
                                   ->whereNotIn('status', ['Closed', 'Ditolak']);
                      });
            })
            ->latest()
            ->get();

        return view('staff.helpdesk.index', [
            'title' => 'Tiket Ditugaskan & Tim',
            'tickets' => $tickets
        ]);
    }

    /**
     * Detail tiket yang diterima staff (Mendukung Hak Akses Tim)
     */
    public function show($id)
    {
        $userId = Auth::id();

        // Staff bisa melihat detail jika dia adalah PIC Utama ATAU tiket tersebut adalah proyek Tim
        $ticket = Ticket::with(['pelapor', 'teknisi'])
            ->where('id', $id)
            ->where(function($query) use ($userId) {
                $query->where('teknisi_id', $userId)
                      ->orWhere('tipe_penyelesaian', 'Tim');
            })
            ->firstOrFail();

        return view('staff.helpdesk.show', [
            'title' => 'Detail Tiket ' . $ticket->no_tiket,
            'ticket' => $ticket
        ]);
    }

    /**
     * Staff mulai mengerjakan tiket
     */
    public function start(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // Validasi Keamanan: Hanya PIC Utama yang boleh memproses
        if ($ticket->teknisi_id != Auth::id()) {
            return back()->with('error', 'Hanya PIC Utama yang dapat memproses tindakan pada tiket ini.');
        }

        // Update status dan waktu mulai
        $ticket->update([
            'status'      => 'Progres',
            'started_at'  => now(),
        ]);

        return back()->with('success', 'Tiket mulai dikerjakan.');
    }

    /**
     * Staff menyelesaikan tiket (Otomatis generate Laporan Tugas)
     */
    public function finish(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // Validasi Keamanan: Hanya PIC Utama yang boleh menyelesaikan
        if ($ticket->teknisi_id != Auth::id()) {
            return back()->with('error', 'Hanya PIC Utama yang dapat menyelesaikan tiket ini.');
        }

        $request->validate([
            'solusi_teknisi' => 'required'
        ]);

        $waktuSelesai = now();

        // 1. Update status tiket menjadi Closed
        $ticket->update([
            'status'          => 'Closed',
            'tgl_selesai'     => $waktuSelesai,
            'solusi_teknisi'  => $request->solusi_teknisi
        ]);

        // 2. OTOMATIS BUAT TASK REPORT (SOP Opsi 1)
        TaskReport::create([
            'staff_id'        => Auth::id(),
            'ticket_id'       => $ticket->id,
            'judul'           => 'Penyelesaian Tiket: ' . $ticket->no_tiket,
            'deskripsi'       => "Masalah: " . $ticket->judul_masalah . "\nDetail: " . ($ticket->deskripsi ?? '-'),
            'hasil'           => $request->solusi_teknisi,
            'tanggal_mulai'   => $ticket->started_at ?? $ticket->created_at,
            'tanggal_selesai' => $waktuSelesai,
        ]);

        // FIX: Route diarahkan ke nama yang benar 'staff.helpdesk.index'
        return redirect()
            ->route('staff.helpdesk.index')
            ->with('success', 'Tiket berhasil diselesaikan dan Laporan Tugas otomatis dibuat!');
    }

    /**
     * Staff menolak tiket yang ditugaskan
     */
    public function reject(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // Validasi Keamanan: Hanya PIC Utama yang boleh menolak
        if ($ticket->teknisi_id != Auth::id()) {
            return back()->with('error', 'Hanya PIC Utama yang dapat menolak tugas ini.');
        }

        $request->validate([
            'alasan_penolakan' => 'required'
        ]);

        $ticket->update([
            'status'             => 'Ditolak',
            'alasan_penolakan'   => $request->alasan_penolakan
        ]);

        // FIX: Route diarahkan ke nama yang benar 'staff.helpdesk.index'
        return redirect()
            ->route('staff.helpdesk.index')
            ->with('success', 'Tugas telah ditolak.');
    }
}