<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ppi;
use App\Exports\PpiExport;
use Maatwebsite\Excel\Facades\Excel;

class PpiAdminController extends Controller
{
    // 1. TAMPILKAN SEMUA REQUEST
    public function index()
    {
        // Ambil semua data, urutkan dari yang terbaru
        $ppi = Ppi::with('user')->latest()->get();

        return view('admin.ppi.index', [
            'title' => 'Monitoring Request PPI',
            'dataPpi' => $ppi
        ]);
    }

    // 2. LIHAT DETAIL (Untuk cek foto/BA Kerusakan)
    public function show($id)
    {
        $ppi = Ppi::with('user')->findOrFail($id);
        return view('admin.ppi.show', [
            'title' => 'Detail PPI ' . $ppi->no_ppi,
            'ppi' => $ppi
        ]);
    }

    // 3. UPDATE STATUS MANUAL (Pending -> Disetujui -> Selesai)
    public function updateStatus(Request $request, $id)
    {
        $ppi = Ppi::findOrFail($id);
        
        // Validasi input status harus sesuai ENUM (UPDATED: Tambah pending_superadmin)
        $request->validate([
            'status' => 'required|in:pending,pending_superadmin,disetujui,selesai,ditolak'
        ]);

        $ppi->status = $request->status;
        $ppi->save();

        return back()->with('success', 'Status PPI berhasil diperbarui jadi ' . ucfirst($request->status));
    }

    // 4. [BARU] FORWARD KE SUPER ADMIN (Untuk Approval)
    public function forwardToSuperAdmin($id)
    {
        $ppi = Ppi::findOrFail($id);

        // Validasi: Hanya bisa diteruskan jika status masih pending
        if ($ppi->status != 'pending') {
            return back()->with('error', 'Hanya PPI status Pending yang bisa diteruskan.');
        }

        // ================================================================
        // PERBAIKAN DI SINI WAK (SAYA KOMENTAR DULU BIAR GAK ERROR)
        // ================================================================
        // Cek apakah User Pemohon sudah TTD (Opsional, tapi disarankan)
        
        // if (empty($ppi->ttd_pemohon)) {
        //    return back()->with('error', 'User belum tanda tangan! Tidak bisa diteruskan.');
        // }
        
        // ================================================================

        // Update status agar muncul di dashboard Super Admin
        $ppi->status = 'pending_superadmin';
        $ppi->save();

        return back()->with('success', 'Berhasil diteruskan ke Super Admin untuk persetujuan.');
    }

    // 5. EXPORT EXCEL
    public function exportExcel(Request $request)
    {
        // Ambil input dari form modal
        $tanggal    = $request->input('tanggal');    // Filter Harian
        $bulan      = $request->input('bulan');      // Filter Bulan
        $tahun      = $request->input('tahun');      // Filter Tahun
        $perusahaan = $request->input('perusahaan'); // Filter Perusahaan

        // Logika Penamaan File Dinamis
        if ($tanggal) {
            $nama_file = 'Laporan-PPI-Harian-' . $tanggal . '.xlsx';
        } elseif ($bulan && $tahun) {
            $nama_bulan = date("F", mktime(0, 0, 0, $bulan, 10)); 
            $nama_file = 'Laporan-PPI-' . $nama_bulan . '-' . $tahun . '.xlsx';
        } elseif ($tahun) {
            $nama_file = 'Laporan-PPI-Tahun-' . $tahun . '.xlsx';
        } else {
            $nama_file = 'Laporan-PPI-Semua-Data.xlsx';
        }

        // Tambahkan suffix perusahaan jika ada
        if ($perusahaan && $perusahaan != 'semua') {
            $nama_file = str_replace('.xlsx', '', $nama_file) . '-' . $perusahaan . '.xlsx';
        }
        
        return Excel::download(new PpiExport($tanggal, $bulan, $tahun, $perusahaan), $nama_file);
    }
}