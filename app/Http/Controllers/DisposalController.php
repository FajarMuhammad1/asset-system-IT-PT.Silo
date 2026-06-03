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
        // 1. Variabel Title untuk Layout Header
        $title = "Manajemen Disposal";

        // 2. Mengambil data disposal beserta relasi aset fisiknya (barangMasuk) 
        //    dan nama/tipe dari master barang, diurutkan dari yang terbaru.
        $disposals = Disposal::with(['barangMasuk.masterBarang', 'pengaju'])->latest()->get();
        
        // 3. Menampilkan list barang yang tersedia untuk diajukan disposal.
        //    Kita filter agar barang yang sudah "Dimusnahkan" tidak muncul lagi di form Admin.
        $availableAssets = BarangMasuk::with('masterBarang')
                            ->where('status', '!=', 'Dimusnahkan')
                            ->get(); 

        return view('admin.disposal.index', compact('title', 'disposals', 'availableAssets'));
    }

    /**
     * Menyimpan pengajuan transaksi Disposal baru.
     * Khusus dilakukan oleh Role: Admin (IT Lokal).
     */
    public function store(Request $request)
    {
        // PENGAMAN: Menggunakan Facade Auth::user() dan kolom 'jenis_user'
        if (!Auth::check() || strtolower(trim(Auth::user()->jenis_user)) !== 'admin') {
            return back()->with('error', 'Akses ditolak! Hanya Admin lokal yang dapat membuat pengajuan.');
        }

        // Validasi inputan form
        $request->validate([
            'barang_masuk_id'   => 'required|exists:barang_masuk,id',
            'reason'            => 'required|string|max:500',
            'data_wiping_proof' => 'required|mimes:pdf,jpg,png,jpeg|max:2048', // Maksimal 2MB
        ]);

        // Proses unggah file bukti data wiping ke dalam folder storage/app/public/disposal_proofs
        $filePath = null;
        if ($request->hasFile('data_wiping_proof')) {
            $filePath = $request->file('data_wiping_proof')->store('disposal_proofs', 'public');
        }
        
        // Simpan data formulir ke tabel disposals
        // Kolom asset_id digunakan untuk mereferensikan id pada tabel barang_masuk
        Disposal::create([
            'asset_id'          => $request->barang_masuk_id, 
            'reason'            => $request->reason,
            'data_wiping_proof' => $filePath,
            'status'            => 'Pending', // Default awal menunggu persetujuan
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
        // PENGAMAN: Cek jenis_user SuperAdmin (atau sesuaikan penulisannya di database kamu, misal 'super admin')
        if (!Auth::check() || strtolower(trim(Auth::user()->jenis_user)) !== 'superadmin') {
            abort(403, 'Akses ditolak! Hanya Super Admin yang berhak menyetujui.');
        }

        // 1. Cari data pengajuan disposal dan ubah status transaksinya menjadi Approved
        $disposal = Disposal::findOrFail($id);
        $disposal->update(['status' => 'Approved']);

        // 2. EFEK DOMINO: Ubah status aset fisik di tabel barang_masuk menjadi 'Dimusnahkan'
        $asset = BarangMasuk::findOrFail($disposal->asset_id);
        $asset->update(['status' => 'Dimusnahkan']); 

        return back()->with('success', 'Disposal disetujui. Status inventaris resmi diubah menjadi Dimusnahkan.');
    }

    /**
     * Proses Penolakan (Reject) Pengajuan Penghapusan Aset.
     * Khusus dilakukan oleh Role: Super Admin (Head Office).
     */
    public function reject($id)
    {
        // PENGAMAN: Cek jenis_user SuperAdmin
        if (!Auth::check() || strtolower(trim(Auth::user()->jenis_user)) !== 'superadmin') {
            abort(403, 'Akses ditolak! Hanya Super Admin yang berhak menolak.');
        }

        // Cari data pengajuan disposal dan ubah status transaksinya menjadi Rejected
        $disposal = Disposal::findOrFail($id);
        $disposal->update(['status' => 'Rejected']);

        return back()->with('warning', 'Pengajuan disposal telah ditolak.');
    }
}