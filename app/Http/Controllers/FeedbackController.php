<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketFeedback;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    // Fungsi untuk menyimpan feedback dari pengguna
    public function store(Request $request, $ticket_id)
    {
        // 1. Validasi input
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_text' => 'nullable|string|max:1000',
        ]);

        // 2. Pastikan tiketnya ada dan sudah selesai (Closed)
        $ticket = Ticket::findOrFail($ticket_id);
        
        if ($ticket->status !== 'Closed') {
            return back()->with('error', 'Feedback hanya bisa diberikan pada tiket yang sudah selesai.');
        }

        // 3. Pastikan user belum pernah memberi rating untuk tiket ini
        $existingFeedback = TicketFeedback::where('ticket_id', $ticket_id)->first();
        if ($existingFeedback) {
            return back()->with('error', 'Anda sudah memberikan penilaian untuk tiket ini.');
        }

        // 4. Simpan ke database
        TicketFeedback::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(), // Mengambil ID user yang sedang login
            'rating' => $request->rating,
            'feedback_text' => $request->feedback_text,
        ]);

        // 5. Kembalikan respons sukses
        return back()->with('success', 'Terima kasih! Penilaian Anda telah kami simpan.');
    }
}