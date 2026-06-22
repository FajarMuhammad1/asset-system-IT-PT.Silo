<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\PerawatanBarang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    // 1. Menampilkan Master Jadwal dan Daftar Tiket Perawatan
    public function index()
    {
        $user = auth()->user();

        // PEMISAHAN LOGIKA TAMPILAN & DATA BERDASARKAN ROLE
        if ($user->role === 'Admin') {
            // -- KHUSUS ADMIN --
            // Ambil data barang untuk form dropdown jadwal
            $barangs = BarangMasuk::with('masterBarang')
                ->whereIn('status', ['Stok', 'Dipakai', 'Digunakan'])
                ->get();

            // Tarik semua Master Jadwal dan semua Tiket
            $schedules = MaintenanceSchedule::with('barangMasuk.masterBarang')->latest()->get();
            $tickets = PerawatanBarang::with(['barangMasuk.masterBarang', 'teknisi'])->latest()->get();

            // Arahkan ke folder view admin
            return view('admin.maintenance.index', compact('schedules', 'tickets', 'barangs'));

        } else {
            // -- KHUSUS STAFF / TEKNISI --
            // Filter tiket: Munculkan hanya tiket yang harus dikerjakan hari ini/sebelumnya
            $tickets = PerawatanBarang::with(['barangMasuk.masterBarang'])
                ->whereIn('status', ['Menunggu', 'Progres'])
                ->latest()
                ->get();

            // Arahkan ke folder view khusus staff
            return view('staff.maintenance.index', compact('tickets'));
        }
    }

    // 2. Menyimpan Master Jadwal Baru (Khusus Admin)
    public function storeSchedule(Request $request)
    {
        $request->validate([
            'barang_masuk_id' => 'required',
            'frekuensi'       => 'required|in:mingguan,bulanan,tahunan',
            'tanggal_mulai'   => 'required|date',
            'deskripsi_tugas' => 'required'
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalNextDue = $tanggalMulai->copy();

        // Jika tanggal mulai adalah masa lalu, atur eksekusi pertama ke hari ini
        if ($tanggalNextDue->isPast()) {
            $tanggalNextDue = Carbon::today();
        }

        MaintenanceSchedule::create([
            'barang_masuk_id'  => $request->barang_masuk_id,
            'frekuensi'        => $request->frekuensi,
            'tanggal_mulai'    => $request->tanggal_mulai,
            'tanggal_next_due' => $tanggalNextDue,
            'deskripsi_tugas'  => $request->deskripsi_tugas,
            'status'           => 'aktif'
        ]);

        return back()->with('success', 'Jadwal rutin berhasil dibuat. Tiket akan muncul otomatis saat waktunya tiba!');
    }

    // 3. Teknisi Menyelesaikan Tiket Perawatan (Menerima input Checklist & Catatan)
    public function selesaikanTiket(Request $request, $id)
    {
        $tiket = PerawatanBarang::findOrFail($id);
        
        // a. Olah array data dari kotak centang (checklist) menjadi teks/kalimat
        $laporanChecklist = "Tindakan: ";
        if ($request->has('checklist') && is_array($request->checklist)) {
            // Menggabungkan pilihan staf (contoh: "Pembersihan Fisik, Update Software.")
            $laporanChecklist .= implode(', ', $request->checklist) . ".";
        } else {
            $laporanChecklist .= "Pengecekan umum (Tidak ada checklist prosedur yang dicentang).";
        }

        // b. Tambahkan catatan manual dari textarea jika staf mengetik keterangan tambahan
        if ($request->filled('catatan_perawatan')) {
            $laporanChecklist .= " | Catatan Tambahan: " . $request->catatan_perawatan;
        }

        // c. Update tiket di database menjadi 'Selesai'
        $tiket->update([
            'status'            => 'Selesai',
            'tanggal_selesai'   => Carbon::now(),
            'catatan_perawatan' => $laporanChecklist, // Menyimpan teks gabungan checklist + catatan
            'teknisi_id'        => auth()->id() // Menandai tiket ini selesai dikerjakan oleh user (staf) yang sedang login
        ]);

        return back()->with('success', 'Laporan checklist maintenance berhasil dikirim!');
    }
}