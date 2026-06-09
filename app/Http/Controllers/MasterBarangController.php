<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class MasterBarangController extends Controller
{
    /**
     * Tampilkan list semua isi katalog (master_barang).
     */
    public function index()
    {
        // Opsional Eager Loading: Jika Anda butuh menghitung jumlah aset per master barang di view,
        // Anda bisa ganti menjadi: MasterBarang::withCount('assets')->latest()->paginate(20);
        $masterBarangList = MasterBarang::latest()->paginate(20);

        return view('admin.masterbarang.index', [
            'title' => 'Master Katalog Barang',
            'masterBarangList' => $masterBarangList
        ]);
    }

    /**
     * Tampilkan form untuk menambah barang baru ke katalog.
     */
    public function create()
    {
        return view('admin.masterbarang.create', [
            'title' => 'Tambah Item Katalog Baru'
        ]);
    }

    /**
     * Simpan barang baru ke katalog.
     */
    public function store(Request $request)
    {
        // 1. Validasi Data
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori'    => 'required|string|max:255',
            'merk'        => 'required|string|max:255',
            'spesifikasi' => 'nullable|string',
        ]);

        // 2. Simpan (PERBAIKAN: Gunakan $validatedData, bukan $request->all())
        MasterBarang::create($validatedData);

        // 3. Redirect dengan pesan sukses
        return redirect()->route('master-barang.index')
                         ->with('success', 'Item katalog baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail item katalog.
     */
    public function show(string $id)
    {
        $masterBarang = MasterBarang::findOrFail($id);
        
        return view('admin.masterbarang.show', [
            'title'        => 'Detail Item Katalog',
            'masterBarang' => $masterBarang
        ]);
    }

    /**
     * Tampilkan form untuk mengedit item katalog.
     */
    public function edit(string $id)
    {
        $masterBarang = MasterBarang::findOrFail($id);

        return view('admin.masterbarang.edit', [
            'title'        => 'Edit Item Katalog',
            'masterBarang' => $masterBarang
        ]);
    }

    /**
     * Update data item katalog.
     */
    public function update(Request $request, string $id)
    {
        $masterBarang = MasterBarang::findOrFail($id);

        // 1. Validasi Data
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori'    => 'required|string|max:255',
            'merk'        => 'required|string|max:255',
            'spesifikasi' => 'nullable|string',
        ]);

        // 2. Update (PERBAIKAN: Gunakan $validatedData, bukan $request->all())
        $masterBarang->update($validatedData);

        // 3. Redirect dengan pesan sukses
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
            // Jika GAGAL karena datanya berelasi/dipakai di tabel lain (Foreign Key Constraint)
            if ($e->errorInfo[1] == 1451) {
                return redirect()->back()
                                 ->with('error', 'Gagal hapus! Item ini sedang dipakai di Surat Jalan atau terikat dengan Aset Fisik.');
            }
            
            // Error database lainnya
            return redirect()->back()->with('error', 'Gagal hapus! Error: ' . $e->getMessage());
        }
    }
}