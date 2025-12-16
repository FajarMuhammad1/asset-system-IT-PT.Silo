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
public function exportExcel(Request $request)
{
    // Ambil input dari form modal
    $bulan = $request->input('bulan');
    $tahun = $request->input('tahun');
    $divisi = $request->input('divisi');

    // Buat nama file dinamis biar keren
    $nama_bulan = $bulan ? date("F", mktime(0, 0, 0, $bulan, 10)) : 'SemuaBulan';
    $nama_file = 'Laporan-PPI-' . $nama_bulan . '-' . $tahun . '.xlsx';
    
    // Kirim data ke Class Export
    return Excel::download(new PpiExport($bulan, $tahun, $divisi), $nama_file);
}

    
}