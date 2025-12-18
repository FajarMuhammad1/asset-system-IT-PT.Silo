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

    // 3. UPDATE STATUS (Pending -> Disetujui -> Selesai)
    public function updateStatus(Request $request, $id)
    {
        $ppi = Ppi::findOrFail($id);
        
        // Validasi input status harus sesuai ENUM
        $request->validate([
            'status' => 'required|in:pending,disetujui,selesai,ditolak'
        ]);

        $ppi->status = $request->status;
        $ppi->save();

        return back()->with('success', 'Status PPI berhasil diperbarui jadi ' . ucfirst($request->status));
    }

    // 4. EXPORT EXCEL (UPDATED)
    public function exportExcel(Request $request)
    {
        // Ambil input dari form modal
        // Pastikan name="" di view/blade sesuai dengan ini
        $tanggal    = $request->input('tanggal');    // Filter Harian
        $bulan      = $request->input('bulan');      // Filter Bulan
        $tahun      = $request->input('tahun');      // Filter Tahun
        $perusahaan = $request->input('perusahaan'); // Filter Perusahaan (Sebelumnya Divisi)

        // Logika Penamaan File Dinamis
        if ($tanggal) {
            // Jika export harian
            $nama_file = 'Laporan-PPI-Harian-' . $tanggal . '.xlsx';
        } elseif ($bulan && $tahun) {
            // Jika export bulanan
            $nama_bulan = date("F", mktime(0, 0, 0, $bulan, 10)); // Ubah angka bulan jadi nama (Januari, dll)
            $nama_file = 'Laporan-PPI-' . $nama_bulan . '-' . $tahun . '.xlsx';
        } elseif ($tahun) {
            // Jika export tahunan saja
            $nama_file = 'Laporan-PPI-Tahun-' . $tahun . '.xlsx';
        } else {
            // Jika tanpa filter waktu (Semua Data)
            $nama_file = 'Laporan-PPI-Semua-Data.xlsx';
        }

        // Tambahkan suffix perusahaan jika ada filter perusahaan
        if ($perusahaan && $perusahaan != 'semua') {
            // Hapus .xlsx dulu, tambah nama perusahaan, baru pasang .xlsx lagi
            $nama_file = str_replace('.xlsx', '', $nama_file) . '-' . $perusahaan . '.xlsx';
        }
        
        // Kirim data ke Class Export dengan urutan parameter baru
        // Urutan: ($tanggal, $bulan, $tahun, $perusahaan)
        return Excel::download(new PpiExport($tanggal, $bulan, $tahun, $perusahaan), $nama_file);
    }
}