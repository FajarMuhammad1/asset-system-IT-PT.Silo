<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk; 
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class AssetLifecycleController extends Controller
{
    public function index()
    {
        $title = "Asset Life Cycle Tracking";
        return view('admin.lifecycle.index', compact('title'));
    }

    public function track(Request $request)
    {
        $request->validate([
            'kode_asset' => 'required|string'
        ]);

        $kodeAsset = $request->kode_asset;
        $title = "Hasil Tracking: " . $kodeAsset;

        // MENGGUNAKAN RELASI JAMAK: perawatanBarangs
        $asset = BarangMasuk::with([
                    'masterBarang', 
                    'mutasiAssets', 
                    'disposal',
                    'perawatanBarangs.teknisi' 
                ])
                ->where('kode_asset', $kodeAsset)
                ->orWhere('serial_number', $kodeAsset)
                ->first();

        if (!$asset) {
            return redirect()->back()->with('error', 'Aset dengan kode/SN tersebut tidak ditemukan di sistem.');
        }

        $history = collect();

        // 1. Pengadaan
        $history->push([
            'tanggal' => $asset->tanggal_masuk ?? $asset->created_at,
            'status' => 'Pengadaan / Masuk',
            'icon' => 'fas fa-box-open bg-primary',
            'oleh' => 'Sistem / Admin',
            'keterangan' => 'Aset (' . ($asset->masterBarang->nama_barang ?? 'Unit') . ') didaftarkan ke sistem dengan status awal: ' . $asset->status
        ]);

        // 2. Distribusi BAST
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
                'oleh' => 'Admin',
                'keterangan' => 'Diserahkan kepada ' . $serah->nama_pengguna . ' (Kondisi saat serah: ' . $serah->kondisi_saat_serah . ')'
            ]);
        }

        // 3. Mutasi
        foreach ($asset->mutasiAssets ?? [] as $mutasi) {
            $history->push([
                'tanggal' => $mutasi->tanggal_mutasi,
                'status' => 'Mutasi Aset',
                'icon' => 'fas fa-exchange-alt bg-warning',
                'oleh' => 'Admin',
                'keterangan' => 'Dipindah dari ' . $mutasi->lokasi_asal . ' ke ' . $mutasi->lokasi_tujuan
            ]);
        }

        // 4. Maintenance (MENGGUNAKAN RELASI JAMAK: perawatanBarangs)
        if ($asset->perawatanBarangs) {
            foreach ($asset->perawatanBarangs as $rawat) {
                $statusMaintenance = ($rawat->status == 'Selesai') ? 'Maintenance Rutin (Selesai)' : 'Maintenance Rutin (' . $rawat->status . ')';
                $warnaIcon = ($rawat->status == 'Selesai') ? 'bg-success' : 'bg-secondary';
                $catatanTindakan = $rawat->catatan_perawatan ?? 'Belum ada catatan/pengerjaan.';
                $namaTeknisi = $rawat->teknisi->nama ?? 'Sistem Penjadwalan';

                $history->push([
                    'tanggal' => $rawat->tanggal_selesai ?? $rawat->tanggal_jadwal,
                    'status' => $statusMaintenance,
                    'icon' => 'fas fa-tools ' . $warnaIcon,
                    'oleh' => $namaTeknisi,
                    'keterangan' => 'Tugas pemeliharaan oleh ' . $namaTeknisi . '. Catatan: "' . $catatanTindakan . '"'
                ]);
            }
        }

        // 5. Disposal
        if ($asset->disposal) {
            $history->push([
                'tanggal' => $asset->disposal->tanggal_disposal,
                'status' => 'Disposal (Dihapuskan)',
                'icon' => 'fas fa-trash-alt bg-danger',
                'oleh' => 'Super Admin',
                'keterangan' => 'Aset dihapuskan dari inventaris resmi. Alasan: ' . $asset->disposal->alasan
            ]);
        }

        $timeline = $history->sortBy('tanggal')->values();

        return view('admin.lifecycle.index', compact('title', 'asset', 'timeline', 'kodeAsset'));
    }

    public function cetakLifecycle($id)
    {
        // MENGGUNAKAN RELASI JAMAK: perawatanBarangs
        $asset = BarangMasuk::with([
            'masterBarang', 
            'mutasiAssets', 
            'disposal',
            'perawatanBarangs.teknisi' 
        ])->findOrFail($id);

        $title = 'Cetak Lifecycle Aset - ' . $asset->kode_asset;

        $history = collect();

        // Pengadaan
        $history->push([
            'tanggal' => $asset->tanggal_masuk ?? $asset->created_at,
            'status' => 'Pengadaan / Masuk',
            'oleh' => 'Sistem / Admin',
            'keterangan' => 'Aset (' . ($asset->masterBarang->nama_barang ?? 'Unit') . ') didaftarkan ke sistem dengan status awal: ' . $asset->status
        ]);

        // Distribusi BAST
        $riwayatSerahTerima = DB::table('log_serah_terima')
            ->join('users', 'log_serah_terima.user_pemegang_id', '=', 'users.id')
            ->where('log_serah_terima.barang_masuk_id', $asset->id)
            ->select('log_serah_terima.*', 'users.nama as nama_pengguna')
            ->get();

        foreach ($riwayatSerahTerima as $serah) {
            $history->push([
                'tanggal' => $serah->tanggal_serah_terima,
                'status' => 'Distribusi (BAST)',
                'oleh' => 'Admin',
                'keterangan' => 'Diserahkan kepada ' . $serah->nama_pengguna . ' (Kondisi saat serah: ' . $serah->kondisi_saat_serah . ')'
            ]);
        }

        // Mutasi
        foreach ($asset->mutasiAssets ?? [] as $mutasi) {
            $history->push([
                'tanggal' => $mutasi->tanggal_mutasi,
                'status' => 'Mutasi Aset',
                'oleh' => 'Admin',
                'keterangan' => 'Dipindah dari ' . $mutasi->lokasi_asal . ' ke ' . $mutasi->lokasi_tujuan
            ]);
        }

        // Maintenance (MENGGUNAKAN RELASI JAMAK: perawatanBarangs)
        if ($asset->perawatanBarangs) {
            foreach ($asset->perawatanBarangs as $rawat) {
                $statusMaintenance = ($rawat->status == 'Selesai') ? 'Maintenance Rutin (Selesai)' : 'Maintenance Rutin (' . $rawat->status . ')';
                $catatanTindakan = $rawat->catatan_perawatan ?? 'Belum ada catatan/pengerjaan.';
                $namaTeknisi = $rawat->teknisi->nama ?? 'Sistem Penjadwalan';

                $history->push([
                    'tanggal' => $rawat->tanggal_selesai ?? $rawat->tanggal_jadwal,
                    'status' => $statusMaintenance,
                    'oleh' => $namaTeknisi,
                    'keterangan' => 'Tugas pemeliharaan oleh ' . $namaTeknisi . '. Catatan: "' . $catatanTindakan . '"'
                ]);
            }
        }

        // Disposal
        if ($asset->disposal) {
            $history->push([
                'tanggal' => $asset->disposal->tanggal_disposal,
                'status' => 'Disposal (Dihapuskan)',
                'oleh' => 'Super Admin',
                'keterangan' => 'Aset dihapuskan dari inventaris resmi. Alasan: ' . $asset->disposal->alasan
            ]);
        }

        $timeline = $history->sortBy('tanggal')->values();

        // UPDATE DI SINI: Mengubah 'admin.asset.cetak_lifecycle' menjadi 'admin.lifecycle.cetak_lifecycle'
        return view('admin.lifecycle.cetak_lifecycle', compact('asset', 'title', 'timeline'));
    }
}