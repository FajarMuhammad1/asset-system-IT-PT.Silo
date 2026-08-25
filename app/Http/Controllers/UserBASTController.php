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
            return redirect()->route('pengguna.userbast.index')
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

        $bast = LogSerahTerima::with('aset')->findOrFail($id);

        // Validasi Pemilik
        if ($bast->user_pemegang_id != Auth::id()) {
            abort(403);
        }

        // Simpan TTD Penerima
        $bast->ttd_penerima = $request->ttd_penerima;

        // Cek apakah Admin sudah TTD terlebih dahulu
        $isAdminSigned = !empty($bast->ttd_petugas);

        if ($isAdminSigned) {
            // Jika Admin sudah TTD & User baru TTD => Status langsung SELESAI
            $bast->status = 'selesai';
            $pesan = 'Tanda tangan BAST berhasil disimpan! Dokumen BAST Resmi Selesai.';

            // Perbarui status Aset menjadi Dipakai
            if ($bast->aset) {
                $bast->aset->update([
                    'status'           => 'Dipakai',
                    'user_pemegang_id' => $bast->user_pemegang_id,
                    'lokasi_sekarang'  => 'User ID: ' . $bast->user_pemegang_id
                ]);

                if ($bast->aset->surat_jalan_id) {
                    $this->checkAndCloseSuratJalan($bast->aset->surat_jalan_id);
                }
            }
        } else {
            // Jika Admin belum TTD => Status menunggu TTD Admin
            $bast->status = 'menunggu_ttd_admin';
            $pesan = 'Tanda tangan berhasil dikirim! Menunggu konfirmasi admin.';
        }

        $bast->save();

        // Update status MutasiAsset jika transaksi ini berasal dari mutasi
        $mutasi = \App\Models\MutasiAsset::where('log_serah_terima_id', $bast->id)->first();
        if ($mutasi) {
            $mutasi->update([
                'status' => 'Selesai'
            ]);

            // Kirim notifikasi ke Pemohon jika ada
            if ($mutasi->pemohon) {
                $mutasi->pemohon->notify(new \App\Notifications\MutasiNotification(
                    $mutasi,
                    'Mutasi Aset Selesai',
                    'Aset ' . ($mutasi->barangMasuk->kode_asset ?? '') . ' telah selesai dimutasi & ditandatangani oleh penerima.',
                    route('pengguna.mutasi.index')
                ));
            }
        }

        // Redirect kembali ke halaman index (Daftar BAST)
        return redirect()->route('pengguna.userbast.index')->with('success', $pesan);
    }

    /**
     * HELPER: Cek Close Surat Jalan
     */
    private function checkAndCloseSuratJalan($suratJalanId)
    {
        $suratJalan = \App\Models\SuratJalan::with('BarangMasuk')->find($suratJalanId);
        if (!$suratJalan) return;

        $totalItems = $suratJalan->barangMasuk->count();
        if ($totalItems === 0) return;

        $completedItems = $suratJalan->barangMasuk
            ->whereIn('status', ['Dipakai', 'Rusak']) 
            ->count();

        if ($completedItems === $totalItems) {
            $suratJalan->update(['is_bast_submitted' => true]);
        } else {
            $suratJalan->update(['is_bast_submitted' => false]);
        }
    }
}