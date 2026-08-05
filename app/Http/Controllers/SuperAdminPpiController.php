<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ppi;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

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
        
        $ttdPath = $request->ttd_superadmin;
        if (str_contains($request->ttd_superadmin, ';base64,')) {
            $image_parts = explode(";base64,", $request->ttd_superadmin);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = 'ttd_sa_ppi_' . uniqid() . '.png';
                $folderPath = public_path('uploads/signatures/');
                
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0777, true, true);
                }

                file_put_contents($folderPath . $fileName, $image_base64);
                $ttdPath = 'uploads/signatures/' . $fileName;
            }
        }

        $ppi->update([
            'status' => 'disetujui', // Status ini membuka kunci Admin buat input SJ
            'ttd_superadmin' => $ttdPath, // Simpan gambar TTD path
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

    // 5. Cetak Dokumen PDF PPI Individual
    public function cetakPdf($id)
    {
        $ppi = Ppi::with('user')->findOrFail($id);

        $pdf = Pdf::loadView('ppi.pdf_single', [
            'ppi' => $ppi
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('PPI_' . str_replace(['/', '\\'], '_', $ppi->no_ppi) . '.pdf');
    }
}