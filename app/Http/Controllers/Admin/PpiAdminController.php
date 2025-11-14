<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ppi;

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

    
}