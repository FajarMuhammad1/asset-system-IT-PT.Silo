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
        $tickets = Ticket::with(['pelapor', 'teknisi'])
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

        $ticket = Ticket::findOrFail($id);

        // Tidak merubah status (tetap Open)
        $ticket->update([
            'teknisi_id' => $request->teknisi_id,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Teknisi berhasil diassign!');
    }
}
