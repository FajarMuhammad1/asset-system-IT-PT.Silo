<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\SuratJalan;
use App\Models\MasterBarang;
use App\Models\User; // <-- 1. TAMBAH INI
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- 2. TAMBAH INI

class BarangMasukController extends Controller
{
    /**
     * Tampilkan SEMUA ASET FISIK
     */
    public function index()
    {
        // Ambil semua aset + relasinya (Biar gak boros query)
        $barangMasuk = BarangMasuk::with('masterBarang', 'suratJalan', 'pemegang')
                                  ->latest()
                                  ->get();

        return view('admin.barangmasuk.index', [
            'barangMasuk' => $barangMasuk,
            'title' => 'Data Aset (Barang Masuk)'
        ]);
    }

    /**
     * Tampilkan FORM tambah aset ("Otomatis Ke Isi")
     */
    public function create()
    {
        // Ambil daftar SJ
        $daftarSuratJalan = SuratJalan::all();
        // Ambil daftar Katalog
        $daftarMasterBarang = MasterBarang::all();

        return view('admin.barangmasuk.create', [
            'title' => 'Tambah Aset (Barang Masuk)',
            'daftarSuratJalan' => $daftarSuratJalan,
            'daftarMasterBarang' => $daftarMasterBarang
        ]);
    }

    /**
     * Simpan 1 ASET FISIK baru
     */
    public function store(Request $request)
    {
        // VALIDASI BARU (Fokus ke fisik)
        $request->validate([
            'surat_jalan_id' => 'required|exists:surat_jalan,id_sj',
            'master_barang_id' => 'required|exists:master_barang,id',
            'serial_number' => 'required|string|max:255|unique:barang_masuk,serial_number',
            'kode_asset' => 'required|string|max:255|unique:barang_masuk,kode_asset',
            'tanggal_masuk' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);
        
        $data = $request->all();
        
        // Tambahin status default
        $data['status'] = 'Stok'; // Otomatis "Stok"
        $data['user_pemegang_id'] = null; // Otomatis "Kosong"

        BarangMasuk::create($data);

        // Redirect balik ke form 'create' biar bisa input SN berikutnya
        return redirect()->route('barangmasuk.create')
                         ->with('success', 'Aset baru (SN: ' . $request->serial_number . ') berhasil ditambahkan! Silakan input lagi.');
    }

    /**
     * Tampilkan detail 1 ASET
     */
    public function show($id)
    {
        $barangMasuk = BarangMasuk::with('masterBarang', 'suratJalan', 'pemegang')
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
        // 3. UBAH INI (Tambah 'with' biar efisien)
        $barangMasuk = BarangMasuk::with('suratJalan', 'masterBarang', 'pemegang')
                                  ->findOrFail($id);
        
        $daftarSuratJalan = SuratJalan::all();
        $daftarMasterBarang = MasterBarang::all();
        $users = User::all(); // 4. TAMBAH INI (Wajib buat dropdown pemegang)

        return view('admin.barangmasuk.edit', [
            'title' => 'Edit Aset',
            'barangMasuk' => $barangMasuk, // Data lama
            'daftarSuratJalan' => $daftarSuratJalan,
            'daftarMasterBarang' => $daftarMasterBarang,
            'users' => $users // 5. TAMBAH INI
        ]);
    }

    /**
     * Update 1 ASET
     */
    public function update(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        $request->validate([
            'surat_jalan_id' => 'required|exists:surat_jalan,id_sj',
            'master_barang_id' => 'required|exists:master_barang,id',
            
            // 6. UBAH VALIDASI UNIQUE BIAR GAK ERROR PAS UPDATE
            'serial_number' => [
                'required', 'string', 'max:255',
                Rule::unique('barang_masuk')->ignore($id, 'id')
            ],
            'kode_asset' => [
                'required', 'string', 'max:255',
                Rule::unique('barang_masuk')->ignore($id, 'id')
            ],
            // -------------------------------------------------

            'tanggal_masuk' => 'required|date',
            'keterangan' => 'nullable|string',
            'status' => 'required|string', // (Harusnya 'status' bisa di-edit di sini)
            'user_pemegang_id' => 'nullable|exists:users,id' // 7. TAMBAH INI
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
}