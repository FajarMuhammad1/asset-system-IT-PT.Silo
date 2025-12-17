<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\SuratJalan;
use App\Models\MasterBarang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorHTML; // Library Barcode

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
        // 1. VALIDASI
        $request->validate([
            'surat_jalan_id'   => 'required|exists:surat_jalan,id_sj',
            'master_barang_id' => 'required|exists:master_barang,id',
            'tanggal_masuk'    => 'required|date',
            'keterangan'       => 'nullable|string',
            'serial_number'    => 'nullable|string|max:255|unique:barang_masuk,serial_number',
        ]);

        DB::beginTransaction();
        
        try {
            // 2. AMBIL DATA MASTER BARANG
            $master = MasterBarang::findOrFail($request->master_barang_id);
            
            // Ambil kategori string
            $namaKategori = $master->kategori ?? 'Umum';

            // 3. CEK APAKAH BARANG CONSUMABLE (Habis Pakai)?
            $consumableKeywords = ['Tinta', 'Cartridge', 'Kertas', 'Kabel', 'ATK', 'Mouse', 'Keyboard', 'Spidol', 'Habis Pakai']; 
            $isConsumable = false;

            foreach ($consumableKeywords as $keyword) {
                if (stripos($namaKategori, $keyword) !== false) {
                    $isConsumable = true;
                    break;
                }
            }

            $kodeAssetFinal = null; 

            // 4. GENERATE KODE UNIK (JIKA BUKAN CONSUMABLE)
            if (!$isConsumable) {
                
                // Tentukan Prefix
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

                // Cari barang terakhir dengan prefix sama
                $lastItem = BarangMasuk::where('kode_asset', 'like', $prefix . '-%')
                                       ->orderBy('id', 'desc')
                                       ->first();

                if ($lastItem) {
                    $lastNumber = intval(substr($lastItem->kode_asset, 4)); 
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1; 
                }

                $kodeAssetFinal = $prefix . '-' . sprintf('%05d', $nextNumber);
            }

            // 5. SIMPAN KE DATABASE
            BarangMasuk::create([
                'kode_asset'       => $kodeAssetFinal,
                'serial_number'    => $request->serial_number,
                'master_barang_id' => $request->master_barang_id,
                'surat_jalan_id'   => $request->surat_jalan_id,
                'tanggal_masuk'    => $request->tanggal_masuk,
                'keterangan'       => $request->keterangan,
                'status'           => 'Stok',       
                'user_pemegang_id' => null,         
            ]);

            DB::commit();

            $pesan = $isConsumable 
                ? 'Barang Habis Pakai berhasil ditambahkan (Tanpa Kode Aset).' 
                : 'Aset berhasil ditambahkan! Kode: ' . $kodeAssetFinal;

            return redirect()->route('barangmasuk.index')->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan detail 1 ASET
     */
    public function show($id)
    {
        $barangMasuk = BarangMasuk::with(['masterBarang', 'suratJalan.ppi', 'pemegang'])
                                  ->findOrFail($id);

        return view('admin.barangmasuk.show', [
            'barangMasuk' => $barangMasuk,
            'title' => 'Detail Aset'
        ]);
    }

    /**
     * Tampilkan form EDIT 1 ASET
     */
    public function edit($id)
    {
        $barangMasuk = BarangMasuk::with(['suratJalan', 'masterBarang', 'pemegang'])
                                  ->findOrFail($id);
        
        $daftarSuratJalan = SuratJalan::all();
        $daftarMasterBarang = MasterBarang::all();
        $users = User::all(); 

        return view('admin.barangmasuk.edit', [
            'title' => 'Edit Aset',
            'barangMasuk' => $barangMasuk, 
            'daftarSuratJalan' => $daftarSuratJalan,
            'daftarMasterBarang' => $daftarMasterBarang,
            'users' => $users 
        ]);
    }

    /**
     * Update 1 ASET
     */
    public function update(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        $request->validate([
            'surat_jalan_id'   => 'required|exists:surat_jalan,id_sj',
            'master_barang_id' => 'required|exists:master_barang,id',
            'serial_number' => [
                'nullable', 'string', 'max:255',
                Rule::unique('barang_masuk')->ignore($id, 'id')
            ],
            'kode_asset' => [
                'nullable', 'string', 'max:255',
                Rule::unique('barang_masuk')->ignore($id, 'id')
            ],
            'tanggal_masuk'    => 'required|date',
            'keterangan'       => 'nullable|string',
            'status'           => 'required|string', 
            'user_pemegang_id' => 'nullable|exists:users,id' 
        ]);

        $barangMasuk->update($request->all());

        return redirect()->route('barangmasuk.index')
                         ->with('success', 'Data Aset berhasil diperbarui!');
    }

    /**
     * Hapus 1 ASET
     */
    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        $barangMasuk->delete();

        return redirect()->route('barangmasuk.index')
                         ->with('success', 'Data Aset berhasil dihapus!');
    }

    // ==========================================
    //            FITUR SCAN & CETAK
    // ==========================================

    /**
     * Halaman Utama Scanner (Kamera & USB)
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

        // Cari aset berdasarkan kode_asset
        $asset = BarangMasuk::where('kode_asset', $request->kode_asset)->first();

        if ($asset) {
            return redirect()->route('barangmasuk.show', $asset->id)
                             ->with('success', 'Aset ditemukan: ' . $asset->kode_asset);
        } else {
            return redirect()->route('scan.index')
                             ->with('error', 'Aset dengan kode "' . $request->kode_asset . '" TIDAK DITEMUKAN!');
        }
    }

    /**
     * Cetak Label Sticker
     */
    public function cetakLabel($id)
    {
        $aset = BarangMasuk::with('masterBarang')->findOrFail($id);

        $generator = new BarcodeGeneratorHTML();
        $barcode = $generator->getBarcode($aset->kode_asset, $generator::TYPE_CODE_128, 2, 60);

        return view('admin.barangmasuk.cetak_label', [
            'aset' => $aset,
            'barcode' => $barcode,
            'title' => 'Cetak Label - ' . $aset->kode_asset
        ]);
    }
}