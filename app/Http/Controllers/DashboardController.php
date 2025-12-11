<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // PENTING
use App\Models\User;
use App\Models\Ppi;
use App\Models\BarangMasuk;
use App\Models\Ticket; 
use App\Models\LogSerahTerima; 

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // 1. DATA KARTU ATAS (STATISTIK UTAMA)
        // ==========================================
        $teamCount = User::whereIn('role', ['Admin', 'SuperAdmin', 'Staff'])->count();
        $penggunaCount = User::where('role', 'Pengguna')->count();
        $assetCount = BarangMasuk::count();
        
        // PENGAMAN: Cek jika tabel belum ada
        try {
            $ppiPendingCount = Ppi::where('status', 'pending')->count();
        } catch (\Exception $e) { $ppiPendingCount = 0; }
        
        try {
            $ticketOpenCount = Ticket::whereIn('status', ['Open', 'Progres'])->count();
        } catch (\Exception $e) { $ticketOpenCount = 0; }


        // ==========================================
        // 2. DATA PROGRESS BAR (STATUS ASET)
        // ==========================================
        $dipakaiCount = BarangMasuk::where('status', 'Dipakai')->count();
        $stokCount    = BarangMasuk::where('status', 'Stok')->count();
        $rusakCount   = BarangMasuk::where('status', 'Rusak')->count();


        // ==========================================
        // 3. DATA TABEL (RIWAYAT BAST TERAKHIR)
        // ==========================================
        $recentBast = LogSerahTerima::with(['aset.masterBarang', 'pemegang'])
            ->orderBy('created_at', 'desc')->limit(5)->get();


        // ==========================================
        // 4. CHART AREA: TREN MAINTENANCE BULANAN
        // ==========================================
        try {
            $monthlyStats = Ticket::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

            $maintenanceData = [];
            for ($i = 1; $i <= 12; $i++) {
                $maintenanceData[] = $monthlyStats[$i] ?? 0;
            }
        } catch (\Exception $e) {
            $maintenanceData = array_fill(0, 12, 0); 
        }


        // ==========================================
        // 5. CHART PIE: STATISTIK DEPARTEMEN
        // ==========================================
        try {
            $ticketStats = DB::table('tickets')
                ->join('users', 'tickets.pelapor_id', '=', 'users.id')
                ->select('users.departemen', DB::raw('count(*) as total'))
                ->groupBy('users.departemen')
                ->get();

            $deptLabels = $ticketStats->pluck('departemen')->map(fn($i) => $i ?? 'Lainnya');
            $deptData = $ticketStats->pluck('total');
        } catch (\Exception $e) {
            $deptLabels = []; $deptData = [];
        }


        // ==========================================
        // 6. TABEL TOP STAFF (BERDASARKAN TASK REPORT)
        // ==========================================
        // [FIXED] Menggunakan kolom 'staff_id' sesuai file SQL kamu
        
        try {
            $topTechnicians = DB::table('task_reports')
                // PERBAIKAN UTAMA DISINI: pakai 'staff_id'
                ->join('users', 'task_reports.staff_id', '=', 'users.id') 
                
                ->select('users.name', 'users.jabatan', DB::raw('count(*) as total_task'))
                ->groupBy('users.id', 'users.name', 'users.jabatan')
                ->orderByDesc('total_task')
                ->limit(5)
                ->get();
                
        } catch (\Exception $e) {
            // Jika error, kembalikan array kosong agar dashboard tetap jalan
            $topTechnicians = [];
        }


        // ==========================================
        // RETURN VIEW
        // ==========================================
        return view('admin.dashboard', [
            'title'           => 'Dashboard Admin',
            'teamCount'       => $teamCount,
            'penggunaCount'   => $penggunaCount,
            'assetCount'      => $assetCount,
            'ppiPendingCount' => $ppiPendingCount,
            'ticketOpenCount' => $ticketOpenCount,
            
            'dipakaiCount'    => $dipakaiCount,
            'stokCount'       => $stokCount,
            'rusakCount'      => $rusakCount,
            
            'recentBast'      => $recentBast,
            
            'deptLabels'      => $deptLabels,
            'deptData'        => $deptData,
            
            'maintenanceData' => $maintenanceData,
            'topTechnicians'  => $topTechnicians
        ]);
    }
}