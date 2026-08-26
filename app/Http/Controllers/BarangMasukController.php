<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\SuratJalan;
use App\Models\MasterBarang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorHTML; // Library Barcode HTML
use Picqer\Barcode\BarcodeGeneratorSVG;  // Library Barcode SVG Vector

// --- UPDATE BAGIAN INI ---
// Pastikan nama file export sesuai dengan yang kamu buat (BarangMasukExport)
use App\Exports\BarangMasukExport; 
use Maatwebsite\Excel\Facades\Excel;

class BarangMasukController extends Controller
{
    /**
     * Tampilkan SEMUA ASET FISIK
     */
    public function index()
    {
        // Load relasi yang diperlukan (Termasuk PPI via Surat Jalan)
        $barangMasuk = BarangMasuk::with(['masterBarang', 'suratJalan.ppi', 'pemegang'])
                                  ->latest()
                                  ->get();

        return view('admin.barangmasuk.index', [
            'barangMasuk' => $barangMasuk,
            'title' => 'Data Aset (Barang Masuk)'
        ]);
    }

    /**
     * FITUR BARU: Export Excel Data Aset
     */
    public function exportExcel(Request $request)
    {
        // 1. Buat Nama File (Ada tanggal & jam biar unik)
        $nama_file = 'Laporan-Asset-IT-' . date('d-m-Y_H-i') . '.xlsx';

        // 2. Download Excel
        // Kita kirim seluruh object $request ke Class Export
        // Biar class Export yang menangani logic filternya
        return Excel::download(new BarangMasukExport($request), $nama_file);
    }

    /**
     * Tampilkan FORM tambah aset
     */
    public function create()
    {
        $daftarSuratJalan = SuratJalan::all();
        $daftarMasterBarang = MasterBarang::all();

        return view('admin.barangmasuk.create', [
            'title' => 'Tambah Aset (Barang Masuk)',
            'daftarSuratJalan' => $daftarSuratJalan,
            'daftarMasterBarang' => $daftarMasterBarang
        ]);
    }

    /**
     * Simpan 1 ASET FISIK baru (AUTO GENERATE CODE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'surat_jalan_id'   => 'required|exists:surat_jalan,id_sj',
            'master_barang_id' => 'required|exists:master_barang,id',
            'tanggal_masuk'    => 'required|date',
            'keterangan'       => 'nullable|string',
            'serial_number'    => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $master = MasterBarang::findOrFail($request->master_barang_id);
            $namaKategori = $master->kategori ?? 'Umum';

            $consumableKeywords = ['Tinta', 'Cartridge', 'Kertas', 'Kabel', 'ATK', 'Mouse', 'Keyboard', 'Spidol', 'Habis Pakai']; 
            $isConsumable = false;

            foreach ($consumableKeywords as $keyword) {
                if (stripos($namaKategori, $keyword) !== false) {
                    $isConsumable = true;
                    break;
                }
            }

            $kodeAssetFinal = null; 

            if (!$isConsumable) {
                $prefix = 'AST'; 
                if (stripos($namaKategori, 'Laptop') !== false)       $prefix = 'LPT';
                elseif (stripos($namaKategori, 'Komputer') !== false) $prefix = 'PC';
                elseif (stripos($namaKategori, 'PC') !== false)       $prefix = 'PC';
                elseif (stripos($namaKategori, 'Printer') !== false)  $prefix = 'PRN';
                elseif (stripos($namaKategori, 'Server') !== false)   $prefix = 'SRV';
                elseif (stripos($namaKategori, 'Switch') !== false)   $prefix = 'SWT';
                elseif (stripos($namaKategori, 'Router') !== false)   $prefix = 'RTR';
                elseif (stripos($namaKategori, 'Proyektor') !== false)$prefix = 'PRJ';
                elseif (stripos($namaKategori, 'Scanner') !== false)  $prefix = 'SCN';
                elseif (stripos($namaKategori, 'Monitor') !== false)  $prefix = 'MON';
                else {
                    $prefix = strtoupper(substr($namaKategori, 0, 3));
                }

                $lastItem = BarangMasuk::where('kode_asset', 'like', $prefix . '-%')
                                       ->orderBy('id', 'desc')
                                       ->first();

                if ($lastItem) {
                    $lastNumber = intval(substr($lastItem->kode_asset, strlen($prefix) + 1)); 
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1; 
                }

                $kodeAssetFinal = $prefix . '-' . sprintf('%05d', $nextNumber);
            }

            $barangMasuk = BarangMasuk::create([
                'surat_jalan_id'   => $request->surat_jalan_id,
                'master_barang_id' => $request->master_barang_id,
                'kode_asset'       => $kodeAssetFinal,
                'serial_number'    => $request->serial_number,
                'tanggal_masuk'    => $request->tanggal_masuk,
                'status'           => 'Tersedia',
                'lokasi_saat_ini'  => 'Gudang IT Utama',
                'user_pemegang_id' => null,
                'keterangan'       => $request->keterangan,
            ]);

            DB::commit();

            $pesan = $isConsumable 
                ? 'Barang Habis Pakai berhasil ditambahkan (Tanpa Kode Aset).' 
                : 'Aset berhasil ditambahkan! Kode: ' . $kodeAssetFinal;

            return redirect()->route('barangmasuk.index')->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan DETAIL aset
     */
    public function show($id)
    {
        $barangMasuk = BarangMasuk::with(['suratJalan', 'masterBarang', 'pemegang'])->findOrFail($id);

        return view('admin.barangmasuk.show', [
            'barangMasuk' => $barangMasuk,
            'title' => 'Detail Aset - ' . $barangMasuk->kode_asset
        ]);
    }

    /**
     * Tampilkan FORM edit aset
     */
    public function edit($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        $daftarSuratJalan = SuratJalan::all();
        $daftarMasterBarang = MasterBarang::all();
        $users = User::orderBy('nama', 'asc')->get();

        return view('admin.barangmasuk.edit', [
            'barangMasuk' => $barangMasuk,
            'daftarSuratJalan' => $daftarSuratJalan,
            'daftarMasterBarang' => $daftarMasterBarang,
            'users' => $users,
            'title' => 'Edit Data Aset'
        ]);
    }

    /**
     * UPDATE data aset
     */
    public function update(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        $request->validate([
            'surat_jalan_id'   => 'required|exists:surat_jalan,id_sj',
            'master_barang_id' => 'required|exists:master_barang,id',
            'serial_number'    => 'nullable|string|max:255',
            'status'           => 'required|string|max:50',
            'user_pemegang_id' => 'nullable|exists:users,id',
            'tanggal_masuk'    => 'required|date',
            'keterangan'       => 'nullable|string',
        ]);

        try {
            $barangMasuk->update([
                'surat_jalan_id'   => $request->surat_jalan_id,
                'master_barang_id' => $request->master_barang_id,
                'serial_number'    => $request->serial_number,
                'status'           => $request->status,
                'user_pemegang_id' => $request->user_pemegang_id,
                'tanggal_masuk'    => $request->tanggal_masuk,
                'keterangan'       => $request->keterangan,
            ]);

            return redirect()->route('barangmasuk.index')
                             ->with('success', 'Data aset ' . $barangMasuk->kode_asset . ' berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    /**
     * HAPUS data aset
     */
    public function destroy($id)
    {
        try {
            $barangMasuk = BarangMasuk::findOrFail($id);
            $kode = $barangMasuk->kode_asset;
            $barangMasuk->delete();

            return redirect()->route('barangmasuk.index')
                             ->with('success', 'Aset ' . $kode . ' berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('barangmasuk.index')
                             ->with('error', 'Gagal menghapus aset. Aset ini mungkin sedang terikat dengan data mutasi/maintenance.');
        }
    }

    /**
     * Tampilkan Halaman Scanner Barcode Aset
     */
    public function scanPage()
    {
        return view('admin.scan.index', [
            'title' => 'Scan Barcode Aset'
        ]);
    }

    /**
     * Proses Logic Scan (Menerima input kode dari alat scan)
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'kode_asset' => 'required'
        ]);

        $inputCode = trim($request->kode_asset);
        // Normalisasi simbol (_ menjadi - dan sebaliknya)
        $codeWithDash = str_replace('_', '-', $inputCode);
        $codeWithUnderscore = str_replace('-', '_', $inputCode);

        // 1. Cari aset berdasarkan kode_asset atau serial_number (Persis atau Partial dengan normalisasi simbol)
        $asset = BarangMasuk::with('masterBarang')
            ->whereIn('kode_asset', [$inputCode, $codeWithDash, $codeWithUnderscore])
            ->orWhereIn('serial_number', [$inputCode, $codeWithDash, $codeWithUnderscore])
            ->orWhere('kode_asset', 'LIKE', '%' . $inputCode . '%')
            ->orWhere('serial_number', 'LIKE', '%' . $inputCode . '%')
            ->first();

        if ($asset) {
            $namaBarang = $asset->masterBarang->nama_barang ?? 'Aset IT';
            return redirect()->route('barangmasuk.show', $asset->id)
                             ->with('success', 'Aset ditemukan: [' . $asset->kode_asset . '] ' . $namaBarang);
        } else {
            return redirect()->route('scan.index')
                             ->with('error_unregistered', $inputCode)
                             ->with('error', 'Kode Barcode "' . htmlspecialchars($inputCode) . '" terdeteksi, namun BELUM TERDAFTAR dalam database aset.');
        }
    }

    /**
     * Cetak Label Sticker Vector SVG Presisi Tinggi
     */
    public function cetakLabel($id)
    {
        $aset = BarangMasuk::with('masterBarang')->findOrFail($id);

        // Gunakan BarcodeGeneratorSVG agar garis barcode vektor sangat tajam dan presisi rasio
        $generator = new BarcodeGeneratorSVG();
        $barcode = $generator->getBarcode($aset->kode_asset, $generator::TYPE_CODE_128, 2, 60);

        return view('admin.barangmasuk.cetak_label', [
            'aset' => $aset,
            'barcode' => $barcode,
            'title' => 'Cetak Label - ' . $aset->kode_asset
        ]);
    }
}