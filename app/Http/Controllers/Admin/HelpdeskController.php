<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

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

        // Ambil semua user dengan role Staff
        $staffList = User::where('role', 'Staff')->get();

        return view('admin.helpdesk.show', [
            'title'     => 'Detail Tiket ' . $ticket->no_tiket,
            'ticket'    => $ticket, // Variabel yang dikirim ke view adalah $ticket
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

        $ticket = Ticket::findOrFail($id);

        // Tidak merubah status (tetap Open)
        $ticket->update([
            'teknisi_id' => $request->teknisi_id,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Teknisi berhasil diassign!');
    }

    /**
     * Update Pengaturan Prioritas dan Tipe Penyelesaian Tiket (Baru)
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
}