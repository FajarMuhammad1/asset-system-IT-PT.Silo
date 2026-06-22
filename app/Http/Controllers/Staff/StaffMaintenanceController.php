<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceSchedule; // Model untuk jadwal maintenance

class StaffMaintenanceController extends Controller
{
    /**
     * Menampilkan daftar jadwal/tiket maintenance untuk Staff
     */
    public function index()
    {
        // 1. Menyediakan data title agar tidak error "Undefined variable $title" di layout
        $title = 'Tugas Perawatan';

        // 2. Mengambil data tiket dari database yang statusnya belum selesai
        $tickets = MaintenanceSchedule::where('status', '!=', 'Selesai')->get(); 
        
        // 3. Mengirim variabel $title dan $tickets ke file Blade view
        return view('staff.maintenance.index', compact('title', 'tickets')); 
    }

    /**
     * Mengeksekusi tombol "Mulai"
     */
    public function mulaiTiket($id)
    {
        // Mengubah status tiket menjadi 'Proses' ketika staff mulai mengerjakan
        $ticket = MaintenanceSchedule::findOrFail($id);
        $ticket->update([
            'status' => 'Proses'
        ]);

        return redirect()->back()->with('success', 'Maintenance berhasil dimulai! Silakan kerjakan.');
    }

    /**
     * Mengeksekusi tombol "Selesai" (Submit form checklist)
     */
    public function selesaikanTiket(Request $request, $id)
    {
        // Validasi opsional jika diperlukan untuk catatan
        $request->validate([
            'catatan_perawatan' => 'nullable|string'
        ]);

        // Mengubah status menjadi 'Selesai' dan menyimpan catatan dari lapangan
        $ticket = MaintenanceSchedule::findOrFail($id);
        $ticket->update([
            'status' => 'Selesai',
            'catatan' => $request->catatan_perawatan // Disesuaikan dengan name="catatan_perawatan" di file Blade
        ]);

        return redirect()->route('staff.maintenance.index')->with('success', 'Laporan maintenance berhasil disubmit!');
    }
}