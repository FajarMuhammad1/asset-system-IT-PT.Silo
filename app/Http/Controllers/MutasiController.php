<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutasiAsset; 
use App\Models\BarangMasuk;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MutasiController extends Controller
{
    public function index()
    {
        $title = "Mutasi Aset";

        // Ambil riwayat mutasi terbaru untuk tabel di bawah form menggunakan MutasiAsset
        $riwayatMutasi = MutasiAsset::with(['barangMasuk.masterBarang', 'userAsal', 'userTujuan'])->latest()->get();

        // Ambil aset yang berstatus 'Stok' atau 'Digunakan' (Kecuali Dimusnahkan)
        $assets = BarangMasuk::with(['masterBarang', 'pemegang'])
                    ->where('status', '!=', 'Dimusnahkan')
                    ->get();

        // Ambil list semua pengguna untuk dropdown user tujuan
        $users = User::all();

        return view('admin.mutasi.index', compact('title', 'riwayatMutasi', 'assets', 'users'));
    }

    public function store(Request $request)
    {
        // Validasi input form mutasi (form tetap mengirim barang_masuk_id dari view)
        $request->validate([
            'barang_masuk_id' => 'required|exists:barang_masuk,id',
            'user_tujuan_id'  => 'required|exists:users,id',
            'keterangan'      => 'nullable|string',
        ]);

        // Ambil data barang masuk/aset saat ini
        $asset = BarangMasuk::findOrFail($request->barang_masuk_id);

        // 1. Simpan rekam jejak mutasi ke tabel mutasi_assets
        MutasiAsset::create([
            'barang_masuk_id' => $asset->id, // UPDATED: Menggunakan nama kolom standar yang benar
            'user_asal_id'    => $asset->user_pemegang_id, // User lama (bisa bernilai null jika sebelumnya masih stok)
            'user_tujuan_id'  => $request->user_tujuan_id,  // User baru
            'keterangan'      => $request->keterangan,
            'tanggal_mutasi'  => now(),
        ]);

        // 2. Perbarui pemegang baru & status pada data aset asli di tabel barang_masuk
        $asset->update([
            'user_pemegang_id' => $request->user_tujuan_id,
            'status'           => 'Digunakan', // Ubah status menjadi digunakan oleh user tujuan
        ]);

        return redirect()->route('mutasi.index')->with('success', 'Aset berhasil dimutasi ke pengguna baru.');
    }
}