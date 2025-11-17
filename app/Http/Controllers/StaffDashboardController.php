<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use App\Models\Ticket; // <-- (NANTI KALO MODEL HELPDESK UDAH ADA)

class StaffDashboardController extends Controller
{
    public function index()
    {
        $staffId = Auth::id();

        // --- (INI LOGIKA BUAT NANTI KALO FITUR HELPDESK JADI) ---
        // $tugasPending = Ticket::where('staff_id', $staffId)->where('status', 'pending')->count();
        // $tugasProses = Ticket::where('staff_id', $staffId)->where('status', 'diproses')->count();
        // $tugasSelesai = Ticket::where('staff_id', $staffId)->where('status', 'selesai')->count();
        // $recentTugas = Ticket::where('staff_id', $staffId)->where('status', 'pending')->latest()->take(5)->get();
        // ---

        // --- (KITA PAKE DATA DUMMY DULU BIAR GAK ERROR) ---
        $tugasPending = 0;
        $tugasProses = 0;
        $tugasSelesai = 0;
        $recentTugas = collect(); // Bikin koleksi kosong
        // ---

        return view('staff.dashboard', [
            'title' => 'Dashboard Staff',
            'menuDashboardStaff' => 'active', // Buat nyalain sidebar
            
            // Kirim data statistik (dummy)
            'tugasPending' => $tugasPending,
            'tugasProses' => $tugasProses,
            'tugasSelesai' => $tugasSelesai,
            'recentTugas' => $recentTugas,
        ]);
    }
}