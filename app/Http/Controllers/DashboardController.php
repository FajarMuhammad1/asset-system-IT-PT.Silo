<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // PENTING: Untuk Query Chart & Raw SQL
use Illuminate\Support\Str; // Untuk Slug ID Accordion
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
        // 5. CHART PIE: STATISTIK DEPARTEMEN (TIKET)
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
        try {
            $topTechnicians = DB::table('task_reports')
                ->join('users', 'task_reports.staff_id', '=', 'users.id') 
                ->where('users.role', 'Staff') // Filter Staff
                ->select('users.nama', 'users.jabatan', DB::raw('count(*) as total_task'))
                ->groupBy('users.id', 'users.nama', 'users.jabatan')
                ->orderByDesc('total_task')
                ->limit(5)
                ->get();
                
        } catch (\Exception $e) {
            $topTechnicians = [];
        }


        // ==========================================
        // [BARU] 7. STATISTIK PPI (PERUSAHAAN -> DEPARTEMEN)
        // ==========================================
        try {
            // Ambil data: Perusahaan, Departemen, Jumlah PPI
            // Asumsi: Tabel 'ppis' punya relasi ke 'users' via 'user_id'
            $rawPpiStats = DB::table('ppis')
                ->join('users', 'ppis.user_id', '=', 'users.id')
                ->select('users.perusahaan', 'users.departemen', DB::raw('count(*) as total'))
                ->groupBy('users.perusahaan', 'users.departemen')
                ->orderBy('users.perusahaan')
                ->get();

            // Format Data: ['PT A' => ['total' => 10, 'departments' => [['name' => 'HR', 'count' => 5], ...]]]
            $ppiPerCompany = [];

            foreach ($rawPpiStats as $stat) {
                $pt = $stat->perusahaan ?? 'Tanpa Perusahaan';
                $dept = $stat->departemen ?? 'Umum';
                $count = $stat->total;

                // Init Array jika PT belum ada
                if (!isset($ppiPerCompany[$pt])) {
                    $ppiPerCompany[$pt] = [
                        'total_company' => 0,
                        'departments' => []
                    ];
                }

                // Tambah Total
                $ppiPerCompany[$pt]['total_company'] += $count;

                // Tambah Detail Dept
                $ppiPerCompany[$pt]['departments'][] = [
                    'name' => $dept,
                    'count' => $count
                ];
            }

        } catch (\Exception $e) {
            $ppiPerCompany = [];
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
            'topTechnicians'  => $topTechnicians,
            
            'ppiPerCompany'   => $ppiPerCompany // [BARU] Data Accordion PPI
        ]);
    }
}