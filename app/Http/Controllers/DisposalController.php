<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Disposal;
use App\Models\BarangMasuk;
use Illuminate\Support\Facades\Auth;

class DisposalController extends Controller
{
    /**
     * Menampilkan halaman utama modul Disposal (Siklus 6).
     * Bisa diakses oleh Admin (Lokal) & Super Admin (HO).
     */
    public function index()
    {
        $title = "Manajemen Disposal";

        $disposals = Disposal::with(['barangMasuk.masterBarang', 'pengaju'])->latest()->get();
        
        $availableAssets = BarangMasuk::with('masterBarang')
                            ->whereNotIn('status', ['Dimusnahkan', 'Hilang']) // Menghindari barang yang sudah hilang/dimusnahkan muncul kembali
                            ->get(); 

        return view('admin.disposal.index', compact('title', 'disposals', 'availableAssets'));
    }

    /**
     * Menyimpan pengajuan transaksi Disposal baru.
     * Khusus dilakukan oleh Role: Admin (IT Lokal).
     */
    public function store(Request $request)
    {
        // Gunakan kolom 'role' secara langsung tanpa strtolower agar cocok dengan Enum 'Admin'
        if (!Auth::check() || Auth::user()->role !== 'Admin') {
            return back()->with('error', 'Akses ditolak! Hanya Admin lokal yang dapat membuat pengajuan.');
        }

        // VALIDASI UPDATE: Bukti wiping wajib KECUALI jika alasan (reason) adalah 'Hilang'
        $request->validate([
            'barang_masuk_id'   => 'required|exists:barang_masuk,id',
            'reason'            => 'required|string|max:500',
            'data_wiping_proof' => 'required_unless:reason,Hilang|nullable|mimes:pdf,jpg,png,jpeg|max:2048', // Maksimal 2MB, boleh null kalau hilang
        ], [
            'data_wiping_proof.required_unless' => 'Bukti Data Wiping wajib dilampirkan untuk proses pemusnahan fisik aset.'
        ]);

        $filePath = null;
        if ($request->hasFile('data_wiping_proof')) {
            $filePath = $request->file('data_wiping_proof')->store('disposal_proofs', 'public');
        }
        
        // Sesuaikan nama kolom dengan struktur database (barang_masuk_id)
        Disposal::create([
            'barang_masuk_id'   => $request->barang_masuk_id, 
            'reason'            => $request->reason,
            'data_wiping_proof' => $filePath,
            'status'            => 'Pending', 
            'submitted_by'      => Auth::id(),
        ]);

        return redirect()->route('disposal.index')
            ->with('success', 'Pengajuan disposal berhasil dikirim dan menunggu persetujuan.');
    }

    /**
     * Proses Persetujuan (Approval) Penghapusan Aset.
     * Khusus dilakukan oleh Role: Super Admin (Head Office).
     */
    public function approve($id)
    {
        // Gunakan pengecekan langsung ke role 'SuperAdmin'
        if (!Auth::check() || Auth::user()->role !== 'SuperAdmin') {
            abort(403, 'Akses ditolak! Hanya Super Admin yang berhak menyetujui.');
        }

        $disposal = Disposal::findOrFail($id);
        $disposal->update(['status' => 'Approved']);

        // Gunakan kolom barang_masuk_id untuk mencari aset
        $asset = BarangMasuk::findOrFail($disposal->barang_masuk_id);
        
        // LOGIKA UPDATE: Jika alasannya hilang, ubah status barang menjadi 'Hilang'. Jika alasan lain, ubah ke 'Dimusnahkan'
        if ($disposal->reason === 'Hilang') {
            $asset->update(['status' => 'Hilang']);
            $pesanSukses = 'Disposal disetujui. Status inventaris resmi diubah menjadi Hilang.';
        } else {
            $asset->update(['status' => 'Dimusnahkan']); 
            $pesanSukses = 'Disposal disetujui. Status inventaris resmi diubah menjadi Dimusnahkan.';
        }

        return back()->with('success', $pesanSukses);
    }

    /**
     * Proses Penolakan (Reject) Pengajuan Penghapusan Aset.
     * Khusus dilakukan oleh Role: Super Admin (Head Office).
     */
    public function reject($id)
    {
        // Gunakan pengecekan langsung ke role 'SuperAdmin'
        if (!Auth::check() || Auth::user()->role !== 'SuperAdmin') {
            abort(403, 'Akses ditolak! Hanya Super Admin yang berhak menolak.');
        }

        $disposal = Disposal::findOrFail($id);
        $disposal->update(['status' => 'Rejected']);

        return back()->with('warning', 'Pengajuan disposal telah ditolak.');
    }

    /**
     * Cetak Berita Acara Pemusnahan (Disposal).
     * Bisa diakses dari link cetak di tabel index.
     */
    public function print($id)
    {
        $disposal = Disposal::with(['barangMasuk.masterBarang', 'pengaju'])->findOrFail($id);

        return view('admin.disposal.print', compact('disposal'));
    }
}