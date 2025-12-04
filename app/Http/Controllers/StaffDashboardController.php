<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $staffId = Auth::id();

        // --- HITUNG JUMLAH TUGAS BERDASARKAN STATUS ---

        $tugasPending = Ticket::where('teknisi_id', $staffId)
                                ->where('status', 'Open')
                                ->count();

        $tugasProses = Ticket::where('teknisi_id', $staffId)
                                ->where('status', 'Progres')
                                ->count();

        $tugasSelesai = Ticket::where('teknisi_id', $staffId)
                                ->where('status', 'Closed')
                                ->count();

        // --- AMBIL 5 TUGAS TERBARU ---
        $recentTugas = Ticket::where('teknisi_id', $staffId)
                                ->latest()
                                ->take(5)
                                ->get();

        // KIRIM KE VIEW
        return view('staff.dashboard', [
            'title' => 'Dashboard Staff',
            'menuDashboardStaff' => 'active',

            'tugasPending'   => $tugasPending,
            'tugasProses'    => $tugasProses,
            'tugasSelesai'   => $tugasSelesai,
            'recentTugas'    => $recentTugas,
        ]);
    }
}
