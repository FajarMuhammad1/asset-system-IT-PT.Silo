<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Ppi; // Pastikan Model PPI dipanggil
use App\Models\BarangMasuk;
use App\Models\Ticket; 
use App\Models\LogSerahTerima; 

class DashboardController extends Controller
{
    public function index()
    {
        // ... (Kode Bagian 1, 2, 3 Tetap Sama) ...
        $teamCount = User::whereIn('role', ['Admin', 'SuperAdmin', 'Staff'])->count();
        $penggunaCount = User::where('role', 'Pengguna')->count();
        $assetCount = BarangMasuk::count();
        $ppiPendingCount = Ppi::where('status', 'pending')->count();
        $ticketOpenCount = Ticket::whereIn('status', ['Open', 'Progres'])->count(); // Tetap hitung tiket untuk kartu
        
        $dipakaiCount = BarangMasuk::where('status', 'Dipakai')->count();
        $stokCount    = BarangMasuk::where('status', 'Stok')->count();
        $rusakCount   = BarangMasuk::where('status', 'Rusak')->count();

        $recentBast = LogSerahTerima::with(['aset.masterBarang', 'pemegang'])
            ->orderBy('created_at', 'desc')->limit(5)->get();

        // ==========================================
        // 4. CHART AREA: TREN PPI BULANAN (REVISI: Pake tabel PPI)
        // ==========================================
        try {
            // UBAH DARI Ticket:: JADI Ppi::
            $monthlyStats = Ppi::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

            $ppiMonthlyData = []; // Ganti nama variabel biar jelas
            for ($i = 1; $i <= 12; $i++) {
                $ppiMonthlyData[] = $monthlyStats[$i] ?? 0;
            }
        } catch (\Exception $e) {
            $ppiMonthlyData = array_fill(0, 12, 0); 
        }


        // ==========================================
        // 5. CHART PIE: STATISTIK DEPARTEMEN BERDASARKAN PPI (REVISI)
        // ==========================================
        try {
            // UBAH SUMBER DATA KE TABEL PPIS
            $ppiDeptStats = DB::table('ppis')
                ->join('users', 'ppis.user_id', '=', 'users.id') // Join ke pembuat PPI
                ->select('users.departemen', DB::raw('count(*) as total'))
                ->groupBy('users.departemen')
                ->get();

            $deptLabels = $ppiDeptStats->pluck('departemen')->map(fn($i) => $i ?? 'Lainnya');
            $deptData = $ppiDeptStats->pluck('total');
        } catch (\Exception $e) {
            $deptLabels = []; $deptData = [];
        }


        // ... (Kode Bagian 6 & 7 Tetap Sama) ...
        $topTechnicians = []; // (Biarkan kosong atau isi query staff jika perlu)
        
        // Accordion PPI
         try {
            $rawPpiStats = DB::table('ppis')
                ->join('users', 'ppis.user_id', '=', 'users.id')
                ->select('users.perusahaan', 'users.departemen', DB::raw('count(*) as total'))
                ->groupBy('users.perusahaan', 'users.departemen')
                ->orderBy('users.perusahaan')
                ->get();

            $ppiPerCompany = [];
            foreach ($rawPpiStats as $stat) {
                $pt = $stat->perusahaan ?? 'Tanpa Perusahaan';
                $dept = $stat->departemen ?? 'Umum';
                $count = $stat->total;
                if (!isset($ppiPerCompany[$pt])) {
                    $ppiPerCompany[$pt] = ['total_company' => 0, 'departments' => []];
                }
                $ppiPerCompany[$pt]['total_company'] += $count;
                $ppiPerCompany[$pt]['departments'][] = ['name' => $dept, 'count' => $count];
            }
        } catch (\Exception $e) { $ppiPerCompany = []; }

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
            
            // DATA YANG DIUBAH KE PPI
            'ppiMonthlyData'  => $ppiMonthlyData, // Data grafik bulanan PPI
            'deptLabels'      => $deptLabels,     // Label Dept dari PPI
            'deptData'        => $deptData,       // Jumlah Dept dari PPI
            
            'topTechnicians'  => $topTechnicians,
            'ppiPerCompany'   => $ppiPerCompany 
        ]);
    }
}