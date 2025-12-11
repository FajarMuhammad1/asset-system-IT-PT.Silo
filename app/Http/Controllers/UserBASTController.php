<?php

namespace App\Http\Controllers;

use App\Models\LogSerahTerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBASTController extends Controller
{
    /**
     * List BAST yang menunggu tanda tangan user
     * Route Name: userbast.index
     */
    public function index()
    {
        $userId = Auth::id();

        // Mengambil data BAST milik user yang statusnya 'menunggu_ttd_user'
        // Pastikan model LogSerahTerima memiliki relasi 'aset', 'masterBarang', dan 'admin'
        $bastList = LogSerahTerima::with(['aset.masterBarang', 'admin'])
            ->where('user_pemegang_id', $userId)
            ->where('status', 'menunggu_ttd_user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Mengarah ke file: resources/views/user/bast/index.blade.php
        return view('pengguna.bast.index', [
            'title' => 'BAST Menunggu Tanda Tangan Anda',
            'bastList' => $bastList
        ]);
    }

    /**
     * Halaman form tanda tangan user
     * Route Name: userbast.sign
     */
    public function sign($id)
    {
        $bast = LogSerahTerima::with(['aset.masterBarang', 'pemegang', 'admin'])
            ->findOrFail($id);

        // Security 1: Pastikan yang akses adalah pemilik barang
        if ($bast->user_pemegang_id != Auth::id()) {
            abort(403, "Anda tidak berhak melihat dokumen ini.");
        }

        // Security 2: Pastikan statusnya memang belum ditandatangani
        if ($bast->status != 'menunggu_ttd_user') {
            return redirect()->route('userbast.index')
                ->with('error', 'Dokumen ini tidak dalam status perlu tanda tangan.');
        }

        // Mengarah ke file: resources/views/user/bast/sign.blade.php
        return view('pengguna.bast.sign', [
            'title' => 'Tanda Tangan BAST',
            'bast' => $bast
        ]);
    }

    /**
     * Submit TTD user
     * Route Name: userbast.submit
     */
    public function submitSign(Request $request, $id)
    {
        $request->validate([
            'agree' => 'required',          // Checkbox S&K
            'ttd_penerima' => 'required|string' // Gambar Base64
        ]);

        $bast = LogSerahTerima::findOrFail($id);

        // Validasi Pemilik
        if ($bast->user_pemegang_id != Auth::id()) {
            abort(403);
        }

        // Simpan TTD & Update Status
        $bast->ttd_penerima = $request->ttd_penerima;
        $bast->status = 'menunggu_ttd_admin'; // Update status agar admin bisa memproses
        $bast->save();

        // Redirect kembali ke halaman index (Daftar BAST)
        return redirect()->route('pengguna.bast.index')
            ->with('success', 'Tanda tangan berhasil dikirim! Menunggu konfirmasi admin.');
    }
}