<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ppi;
use App\Models\BarangMasuk;
use App\Models\Ticket; // <-- [BARU] Import Model Ticket

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Team IT
        $teamCount = User::whereIn('role', ['Admin', 'SuperAdmin', 'Staff'])->count();
        
        // 2. Hitung Pengguna
        $penggunaCount = User::where('role', 'Pengguna')->count();
        
        // 3. Hitung Aset
        $assetCount = BarangMasuk::count();
        
        // 4. Hitung PPI Pending
        $ppiPendingCount = Ppi::where('status', 'pending')->count();

        // 5. [BARU] Hitung Tiket Helpdesk (Open + Progres)
        // Kita hitung tiket yang belum selesai (Open atau Progres)
        $ticketOpenCount = Ticket::whereIn('status', ['Open', 'Progres'])->count();

        return view('admin.dashboard', [
            'title' => 'Dashboard Admin',
            'teamCount' => $teamCount,
            'penggunaCount' => $penggunaCount,
            'assetCount' => $assetCount,
            'ppiPendingCount' => $ppiPendingCount,
            'ticketOpenCount' => $ticketOpenCount, // <-- Kirim ke View
        ]);
    }
}