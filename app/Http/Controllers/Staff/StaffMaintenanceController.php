<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PerawatanBarang; 
use Carbon\Carbon; // Pastikan Carbon dipanggil untuk tanggal

class StaffMaintenanceController extends Controller
{
    /**
     * Menampilkan daftar tugas perawatan untuk Staff
     */
    public function index()
    {
        // Menyediakan variabel $title untuk digunakan di Blade layout (mengatasi Undefined variable $title)
        $title = 'Tugas Perawatan Aset';

        // Tarik semua tugas yang belum 'Selesai' (Menunggu / Progres)
        $tugasPerawatan = PerawatanBarang::with('barangMasuk.masterBarang')
            ->where('status', '!=', 'Selesai')
            ->get(); 
        
        // Kirim variabel title dan tugasPerawatan ke Blade
        return view('staff.maintenance.index', compact('title', 'tugasPerawatan')); 
    }

    /**
     * Mengeksekusi tombol "Mulai" (Ubah status Menunggu -> Progres)
     */
    public function mulaiPerawatan($id) 
    {
        $perawatan = PerawatanBarang::findOrFail($id);
        
        $perawatan->update([
            'status'     => 'Progres',
            'teknisi_id' => auth()->id() // Mencatat ID Staff yang mengeklik tombol "Mulai"
        ]);

        return redirect()->back()->with('success', 'Tugas perawatan berhasil dimulai! Silakan kerjakan.');
    }

    /**
     * Mengeksekusi tombol "Selesai" (Kirim Laporan Servis)
     */
    public function selesaikanPerawatan(Request $request, $id) 
    {
        // 1. Validasi input, termasuk menangkap array dari form checklist Blade
        $request->validate([
            'checklist'         => 'nullable|array',
            'catatan_perawatan' => 'nullable|string'
        ]);

        $perawatan = PerawatanBarang::findOrFail($id);
        
        // 2. Olah data checklist menjadi format teks (Contoh: "Pembersihan Fisik & Debu, Update Software")
        $checklistTeks = $request->has('checklist') 
            ? implode(', ', $request->checklist) 
            : 'Tidak ada prosedur SOP yang dicentang';

        // 3. Gabungkan hasil checklist dengan catatan yang diinput oleh staff
        $catatanAkhir = "Prosedur Dilakukan: " . $checklistTeks;
        if ($request->filled('catatan_perawatan')) {
            $catatanAkhir .= " | Catatan Lapangan: " . $request->catatan_perawatan;
        }
        
        // 4. Update status dan simpan ke database
        $perawatan->update([
            'status'            => 'Selesai',
            'tanggal_selesai'   => Carbon::now()->format('Y-m-d'), // Simpan tanggal selesai hari ini
            'catatan_perawatan' => $catatanAkhir, // Simpan gabungan checklist dan catatan lapangan
            'teknisi_id'        => auth()->id() // Pastikan staff yang mensubmit terekam
        ]);

        return redirect()->route('staff.maintenance.index')->with('success', 'Laporan perawatan berhasil disubmit!');
    }
}