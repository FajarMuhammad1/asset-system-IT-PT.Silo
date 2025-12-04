<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffHelpdeskController extends Controller
{
    /**
     * Halaman utama staff - daftar tiket yang ditugaskan admin
     */
    public function index()
    {
        $tickets = Ticket::where('teknisi_id', Auth::id())
                         ->latest()
                         ->get();

        return view('staff.helpdesk.index', [
            'title' => 'Tiket Ditugaskan',
            'tickets' => $tickets
        ]);
    }

    /**
     * Detail tiket yang diterima staff
     */
    public function show($id)
{
    $ticket = Ticket::where('id', $id)
                    ->where('teknisi_id', Auth::id())
                    ->firstOrFail();

    return view('staff.helpdesk.show', [
        'title' => 'Detail Tiket',
        'ticket' => $ticket
    ]);
}


    /**
     * Staff mulai mengerjakan tiket
     * - Status berubah menjadi Progres
     * - Mencatat waktu mulai (started_at)
     */
    public function start(Request $request, $id)
    {
        $ticket = Ticket::where('teknisi_id', Auth::id())->findOrFail($id);

        // Update status dan waktu mulai
        $ticket->update([
            'status'      => 'Progres',
            'started_at'  => now(),
        ]);

        return back()->with('success', 'Tiket mulai dikerjakan.');
    }

    /**
     * Staff menyelesaikan tiket
     * - Status menjadi Closed
     * - Mengisi solusi teknis
     * - Mengisi tanggal selesai
     */
    public function finish(Request $request, $id)
    {
        $ticket = Ticket::where('teknisi_id', Auth::id())->findOrFail($id);

        $request->validate([
            'solusi_teknisi' => 'required'
        ]);

        $ticket->update([
            'status'          => 'Closed',
            'tgl_selesai'     => now(),
            'solusi_teknisi'  => $request->solusi_teknisi
        ]);

        return redirect()
            ->route('staff.helpdesk.index')
            ->with('success', 'Tiket berhasil diselesaikan!');
    }

    /**
     * Staff menolak tiket yang ditugaskan
     * - Status menjadi Ditolak
     * - Wajib mengisi alasan penolakan
     */
    public function reject(Request $request, $id)
    {
        $ticket = Ticket::where('teknisi_id', Auth::id())->findOrFail($id);

        $request->validate([
            'alasan_penolakan' => 'required'
        ]);

        $ticket->update([
            'status'             => 'Reject',
            'alasan_penolakan'   => $request->alasan_penolakan
        ]);

        return redirect()
            ->route('staff.helpdesk.index')
            ->with('success', 'Tugas telah ditolak.');
    }
}
