<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
// Import Model
use App\Models\User;
use App\Models\Ppi; 
use App\Models\BarangMasuk;
use App\Models\Ticket; 
use App\Models\LogSerahTerima; 
use App\Models\TaskReport; // <--- PENTING: Tambahkan ini agar bisa dipanggil

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // 1. STATISTIK KARTU ATAS
        // ==========================================
        $teamCount       = User::whereIn('role', ['Admin', 'SuperAdmin', 'Staff'])->count();
        $penggunaCount   = User::where('role', 'Pengguna')->count();
        $assetCount      = BarangMasuk::count();
        $ppiPendingCount = Ppi::where('status', 'pending')->count();
        $ticketOpenCount = Ticket::whereIn('status', ['Open', 'Progres'])->count(); 
        
        // Statistik Aset
        $dipakaiCount = BarangMasuk::where('status', 'Dipakai')->count();
        $stokCount    = BarangMasuk::where('status', 'Stok')->count();
        $rusakCount   = BarangMasuk::where('status', 'Rusak')->count();

        // Riwayat BAST Terakhir
        $recentBast = LogSerahTerima::with(['aset.masterBarang', 'pemegang'])
            ->orderBy('created_at', 'desc')->limit(5)->get();

        // ==========================================
        // 2. CHART AREA: TREN PPI BULANAN
        // ==========================================
        try {
            $monthlyStats = Ppi::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

            $ppiMonthlyData = []; 
            for ($i = 1; $i <= 12; $i++) {
                $ppiMonthlyData[] = $monthlyStats[$i] ?? 0;
            }
        } catch (\Exception $e) {
            $ppiMonthlyData = array_fill(0, 12, 0); 
        }

        // ==========================================
        // 3. CHART PIE: STATISTIK DEPARTEMEN (PPI)
        // ==========================================
        try {
            $ppiDeptStats = DB::table('ppis')
                ->join('users', 'ppis.user_id', '=', 'users.id') 
                ->select('users.departemen', DB::raw('count(*) as total'))
                ->groupBy('users.departemen')
                ->get();

            $deptLabels = $ppiDeptStats->pluck('departemen')->map(fn($i) => $i ?? 'Lainnya');
            $deptData   = $ppiDeptStats->pluck('total');
        } catch (\Exception $e) {
            $deptLabels = []; $deptData = [];
        }

        // ==========================================
        // 4. LEADERBOARD: TOP 5 STAFF IT (UPDATE BARU)
        // ==========================================
        // Mengambil User yang role-nya 'Staff', menghitung jumlah taskReports, dan mengurutkannya.
        // ==========================================
        // 4. LEADERBOARD: TOP 5 STAFF IT (FIXED)
        // ==========================================
        // ==========================================
        // 4. LEADERBOARD: TOP 5 STAFF IT (FIXED)
        // ==========================================
        try {
            // Pastikan role di database = 'Staff'
            $topTechnicians = User::where('role', 'Staff') 
                ->withCount('taskReports') // Memanggil fungsi relasi yang baru kita buat di User.php
                ->orderByDesc('task_reports_count') // Urutkan dari yang tugasnya terbanyak
                ->take(5)
                ->get()
                ->map(function($user) {
                    // Mapping hanya untuk total_task
                    // Kita tidak perlu mapping 'nama' lagi karena di User.php sudah 'nama'
                    $user->total_task = $user->task_reports_count;
                    return $user;
                });
        } catch (\Exception $e) {
            $topTechnicians = [];
        }
        
        // ==========================================
        // 5. ACCORDION PPI PER PERUSAHAAN
        // ==========================================
        try {
            $rawPpiStats = DB::table('ppis')
                ->join('users', 'ppis.user_id', '=', 'users.id')
                ->select('users.perusahaan', 'users.departemen', DB::raw('count(*) as total'))
                ->groupBy('users.perusahaan', 'users.departemen')
                ->orderBy('users.perusahaan')
                ->get();

            $ppiPerCompany = [];
            foreach ($rawPpiStats as $stat) {
                $pt    = $stat->perusahaan ?? 'Tanpa Perusahaan';
                $dept  = $stat->departemen ?? 'Umum';
                $count = $stat->total;
                
                if (!isset($ppiPerCompany[$pt])) {
                    $ppiPerCompany[$pt] = ['total_company' => 0, 'departments' => []];
                }
                $ppiPerCompany[$pt]['total_company'] += $count;
                $ppiPerCompany[$pt]['departments'][] = ['name' => $dept, 'count' => $count];
            }
        } catch (\Exception $e) { 
            $ppiPerCompany = []; 
        }

        // RETURN VIEW
        return view('admin.dashboard', [
            'title'           => 'Dashboard Admin',
            'teamCount'       => $teamCount,
            'penggunaCount'   => $penggunaCount,
            'assetCount'      => $assetCount,
            'ticketOpenCount' => $ticketOpenCount,
            
            'dipakaiCount'    => $dipakaiCount,
            'stokCount'       => $stokCount,
            'rusakCount'      => $rusakCount,
            'recentBast'      => $recentBast,
            
            'ppiMonthlyData'  => $ppiMonthlyData, 
            'deptLabels'      => $deptLabels,     
            'deptData'        => $deptData,       
            
            'topTechnicians'  => $topTechnicians, // <-- Data ini sekarang sudah berisi staff
            'ppiPerCompany'   => $ppiPerCompany 
        ]);
    }
}