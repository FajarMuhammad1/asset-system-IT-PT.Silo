<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk; 
use Illuminate\Support\Facades\DB; 

class AssetLifecycleController extends Controller
{
    public function index()
    {
        $title = "Asset Life Cycle Tracking";
        return view('admin.lifecycle.index', compact('title'));
    }

    public function track(Request $request)
    {
        // Validasi input wajib kode_asset
        $request->validate([
            'kode_asset' => 'required|string'
        ]);

        $kodeAsset = $request->kode_asset;
        $title = "Hasil Tracking: " . $kodeAsset;

        // 1. Ambil data aset beserta relasi murni yang melekat pada barang fisik
        $asset = BarangMasuk::with(['masterBarang', 'mutasiAssets', 'disposal'])
                    ->where('kode_asset', $kodeAsset)
                    ->orWhere('serial_number', $kodeAsset)
                    ->first();

        if (!$asset) {
            return redirect()->back()->with('error', 'Aset dengan kode/SN tersebut tidak ditemukan di sistem.');
        }

        // ==========================================
        // KUMPULKAN RIWAYAT (TIMELINE FOKUS ASET)
        // ==========================================
        $history = collect();

        // 1. Riwayat Pengadaan (Awal mula barang masuk ke gudang)
        $history->push([
            'tanggal' => $asset->tanggal_masuk ?? $asset->created_at,
            'status' => 'Pengadaan / Masuk',
            'icon' => 'fas fa-box-open bg-primary',
            'keterangan' => 'Aset (' . ($asset->masterBarang->nama_barang ?? 'Unit') . ') didaftarkan ke sistem dengan status awal: ' . $asset->status
        ]);

        // 2. Riwayat Distribusi BAST (Saat barang diserahkan ke user dari log_serah_terima)
        $riwayatSerahTerima = DB::table('log_serah_terima')
            ->join('users', 'log_serah_terima.user_pemegang_id', '=', 'users.id')
            ->where('log_serah_terima.barang_masuk_id', $asset->id)
            ->select('log_serah_terima.*', 'users.nama as nama_pengguna')
            ->get();

        foreach ($riwayatSerahTerima as $serah) {
            $history->push([
                'tanggal' => $serah->tanggal_serah_terima,
                'status' => 'Distribusi (BAST)',
                'icon' => 'fas fa-truck-loading bg-info',
                'keterangan' => 'Diserahkan kepada ' . $serah->nama_pengguna . ' (Kondisi saat serah: ' . $serah->kondisi_saat_serah . ')'
            ]);
        }

        // 3. Riwayat Mutasi (Perpindahan kepemilikan / lokasi antar unit)
        foreach ($asset->mutasiAssets ?? [] as $mutasi) {
            $history->push([
                'tanggal' => $mutasi->tanggal_mutasi,
                'status' => 'Mutasi Aset',
                'icon' => 'fas fa-exchange-alt bg-warning',
                'keterangan' => 'Dipindah dari ' . $mutasi->lokasi_asal . ' ke ' . $mutasi->lokasi_tujuan
            ]);
        }

        // 4. Riwayat Disposal (Akhir siklus hidup aset jika dihapuskan/dijual/dimusnahkan)
        if ($asset->disposal) {
            $history->push([
                'tanggal' => $asset->disposal->tanggal_disposal,
                'status' => 'Disposal (Dihapuskan)',
                'icon' => 'fas fa-trash-alt bg-danger',
                'keterangan' => 'Aset dihapuskan dari inventaris resmi. Alasan: ' . $asset->disposal->alasan
            ]);
        }

        // Urutkan timeline secara kronologis (dari tanggal paling lama ke paling baru)
        $timeline = $history->sortBy('tanggal')->values();

        // Kirim data murni aset ke view
        return view('admin.lifecycle.index', compact('title', 'asset', 'timeline', 'kodeAsset'));
    }
}