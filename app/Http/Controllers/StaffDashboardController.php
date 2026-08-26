<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\PerawatanBarang;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $staffId = Auth::id();

        // --- HITUNG JUMLAH TUGAS BERDASARKAN STATUS ---
        $tugasPending = Ticket::where(function($query) use ($staffId) {
                                $query->where('teknisi_id', $staffId)
                                      ->orWhere(function($subQuery) {
                                          $subQuery->where('tipe_penyelesaian', 'Tim')
                                                   ->whereNotIn('status', ['Closed', 'Ditolak']);
                                      });
                            })
                            ->where('status', 'Open')
                            ->count();

        $tugasProses = Ticket::where(function($query) use ($staffId) {
                                $query->where('teknisi_id', $staffId)
                                      ->orWhere(function($subQuery) {
                                          $subQuery->where('tipe_penyelesaian', 'Tim')
                                                   ->whereNotIn('status', ['Closed', 'Ditolak']);
                                      });
                            })
                            ->where('status', 'Progres')
                            ->count();

        $tugasSelesai = Ticket::where('teknisi_id', $staffId)
                                ->where('status', 'Closed')
                                ->count();

        // --- AMBIL DAFTAR PENUGASAN TIKET HELPDESK ---
        $recentTugas = Ticket::with(['pelapor', 'barangMasuk.masterBarang', 'teknisi'])
            ->where(function($query) use ($staffId) {
                $query->where('teknisi_id', $staffId)
                      ->orWhere(function($subQuery) {
                          $subQuery->where('tipe_penyelesaian', 'Tim')
                                   ->whereNotIn('status', ['Closed', 'Ditolak']);
                      });
            })
            ->latest()
            ->take(10)
            ->get();

        // --- AMBIL TUGAS PERAWATAN / MAINTENANCE RUTIN ---
        $tugasPerawatan = PerawatanBarang::with(['barangMasuk.masterBarang', 'teknisi'])
            ->whereIn('status', ['Menunggu', 'Progres'])
            ->latest()
            ->get();

        // KIRIM KE VIEW
        return view('staff.dashboard', [
            'title' => 'Dashboard Staff IT',
            'menuDashboardStaff' => 'active',

            'tugasPending'   => $tugasPending,
            'tugasProses'    => $tugasProses,
            'tugasSelesai'   => $tugasSelesai,
            'recentTugas'    => $recentTugas,
            'tugasPerawatan' => $tugasPerawatan,
        ]);
    }
}
