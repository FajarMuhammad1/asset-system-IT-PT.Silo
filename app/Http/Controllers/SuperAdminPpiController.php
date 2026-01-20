<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ppi;

class SuperAdminPpiController extends Controller
{
    // 1. Halaman List Approval
    public function index()
    {
        // Ambil data yang statusnya menunggu SuperAdmin
        $requestMasuk = Ppi::where('status', 'pending_superadmin')
                           ->orderBy('created_at', 'desc')
                           ->get();
                           
        return view('superadmin.approval_list', [
            'title' => 'Daftar Approval PPI', // <--- Variabel title ditambahkan
            'requestMasuk' => $requestMasuk
        ]);
    }

    // 2. Halaman Detail & TTD
    public function showReview($id)
    {
        $ppi = Ppi::findOrFail($id);
        
        return view('superadmin.approval_review', [
            'title' => 'Review PPI ' . $ppi->no_ppi, // <--- Variabel title ditambahkan
            'ppi' => $ppi
        ]);
    }

    // 3. Aksi Setuju (Simpan TTD SuperAdmin)
    public function approve(Request $request, $id)
    {
        $ppi = Ppi::findOrFail($id);
        
        // Validasi: Pastikan Tanda Tangan terisi
        if (empty($request->ttd_superadmin)) {
            return back()->with('error', 'Tanda tangan diperlukan untuk menyetujui!');
        }
        
        $ppi->update([
            'status' => 'disetujui', // Status ini membuka kunci Admin buat input SJ
            'ttd_superadmin' => $request->ttd_superadmin, // Simpan gambar TTD
            'tgl_approve' => now()
        ]);

        return redirect()->route('superadmin.approval.index')->with('success', 'PPI Disetujui!');
    }

    // 4. Aksi Tolak
    public function reject(Request $request, $id)
    {
        $ppi = Ppi::findOrFail($id);
        
        $ppi->update([
            'status' => 'ditolak',
            'alasan_tolak' => $request->alasan_tolak
        ]);

        return redirect()->route('superadmin.approval.index')->with('success', 'PPI Ditolak.');
    }
}