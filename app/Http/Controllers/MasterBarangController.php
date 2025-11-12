<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang; // <-- PANGGIL MODELNYA
use Illuminate\Http\Request;
use Illuminate\Database\QueryException; // <-- Buat nangkep error pas hapus

class MasterBarangController extends Controller
{
    /**
     * Tampilkan list semua isi katalog (master_barang).
     */
    public function index()
    {
        $masterBarangList = MasterBarang::latest()->paginate(20); // Ambil data, urut dari terbaru

        return view('admin.masterbarang.index', [
            'title' => 'Master Katalog Barang',
            'masterBarangList' => $masterBarangList
        ]);
    }

    /**
     * Tampilkan form buat nambah barang baru ke katalog.
     */
    public function create()
    {
        return view('admin.masterbarang.create', [
            'title' => 'Tambah Item Katalog Baru'
        ]);
    }

    /**
     * Simpen barang baru ke katalog.
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string',
        ]);

        // 2. Simpen
        MasterBarang::create($request->all());

        // 3. Balikin
        return redirect()->route('master-barang.index')
                         ->with('success', 'Item katalog baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail (gak wajib, tapi lo bikinin)
     */
    public function show(string $id)
    {
        $masterBarang = MasterBarang::findOrFail($id);
        
        return view('admin.masterbarang.show', [
            'title' => 'Detail Item Katalog',
            'masterBarang' => $masterBarang
        ]);
    }

    /**
     * Tampilkan form buat ngedit item katalog.
     */
    public function edit(string $id)
    {
        $masterBarang = MasterBarang::findOrFail($id);

        return view('admin.masterbarang.edit', [
            'title' => 'Edit Item Katalog',
            'masterBarang' => $masterBarang
        ]);
    }

    /**
     * Update data item katalog.
     */
    public function update(Request $request, string $id)
    {
        $masterBarang = MasterBarang::findOrFail($id);

        // 1. Validasi
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string',
        ]);

        // 2. Update
        $masterBarang->update($request->all());

        // 3. Balikin
        return redirect()->route('master-barang.index')
                         ->with('success', 'Item katalog berhasil diperbarui.');
    }

    /**
     * Hapus item dari katalog.
     */
    public function destroy(string $id)
    {
        $masterBarang = MasterBarang::findOrFail($id);

        try {
            // Coba Hapus
            $masterBarang->delete();
            return redirect()->route('master-barang.index')
                             ->with('success', 'Item katalog berhasil dihapus.');

        } catch (QueryException $e) {
            // Kalo GAGAL (karena datanya dipake di 'surat_jalan_details')
            // Kita pake kode 1451 untuk foreign key constraint
            if ($e->errorInfo[1] == 1451) {
                return redirect()->back()
                                 ->with('error', 'Gagal hapus! Item ini sedang dipakai di Surat Jalan atau Aset.');
            }
            // Error lain
            return redirect()->back()->with('error', 'Gagal hapus! Error: ' . $e->getMessage());
        }
    }
}