<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // <-- 1. PANGGIL MODEL USER
use App\Models\Ppi;  // <-- 2. PANGGIL MODEL PPI
use App\Models\BarangMasuk; // <-- 3. PANGGIL MODEL ASET

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin.
     */
    public function index()
    {
        // 4. LOGIKA PENGHITUNGAN DATA
        
        // Asumsi Tim IT = Admin, SuperAdmin, dan Staff
        $teamCount = User::whereIn('role', ['Admin', 'SuperAdmin', 'Staff'])->count();
        
        // Hitung total Pengguna (role 'Pengguna')
        $penggunaCount = User::where('role', 'Pengguna')->count();
        
        // Hitung total Aset Fisik (dari tabel barang_masuk)
        $assetCount = BarangMasuk::count();
        
        // Hitung total PPI yang masih 'pending'
        $ppiPendingCount = Ppi::where('status', 'pending')->count();

        // 5. KIRIM DATA KE VIEW
        return view('admin.dashboard', [
            'title' => 'Dashboard Admin',
            'menuDashboard' => 'active', // Buat nyalain sidebar
            'teamCount' => $teamCount,
            'penggunaCount' => $penggunaCount,
            'assetCount' => $assetCount,
            'ppiPendingCount' => $ppiPendingCount,
        ]);
    }
}