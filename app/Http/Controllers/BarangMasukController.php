<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    /**
     * Tampilkan semua data barang masuk.
     */
    public function index()
    {
        $barangMasuk = BarangMasuk::all();

        return view('admin.barangmasuk.index', [
            'barangMasuk' => $barangMasuk,
            'title' => 'Data Barang Masuk'
        ]);
    }

    /**
     * Tampilkan form tambah barang masuk.
     */
    public function create()
    {
        return view('admin.barangmasuk.create', [
            'title' => 'Tambah Barang Masuk'
        ]);
    }

    /**
     * Simpan data baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_sj'          => 'required|string|max:255',
            'no_ppi'         => 'required|string|max:255',
            'no_po'          => 'required|string|max:255',
            'nama_barang'    => 'required|string|max:255',
            'kategori'       => 'required|array',
            'jumlah'         => 'required|integer|min:1',
            'tanggal_masuk'  => 'required|date',
            'keterangan'     => 'nullable|string',
        ]);

        $data = $request->all();
        $data['kategori'] = implode(', ', $request->kategori); // ubah array ke string

        BarangMasuk::create($data);

        return redirect()->route('barangmasuk.index')
            ->with('success', 'Data Barang Masuk berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail barang masuk.
     */
    public function show($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        return view('admin.barangmasuk.show', [
            'barangMasuk' => $barangMasuk,
            'title' => 'Detail Barang Masuk'
        ]);
    }

    /**
     * Tampilkan form edit barang masuk.
     */
    public function edit($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        return view('admin.barangmasuk.edit', [
            'barangMasuk' => $barangMasuk,
            'title' => 'Edit Barang Masuk'
        ]);
    }

    /**
     * Update data barang masuk.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'no_sj'          => 'required|string|max:255',
            'no_ppi'         => 'required|string|max:255',
            'no_po'          => 'required|string|max:255',
            'nama_barang'    => 'required|string|max:255',
            'kategori'       => 'required|array',
            'jumlah'         => 'required|integer|min:1',
            'tanggal_masuk'  => 'required|date',
            'keterangan'     => 'nullable|string',
        ]);

        $barangMasuk = BarangMasuk::findOrFail($id);

        $data = $request->all();
        $data['kategori'] = implode(', ', $request->kategori); // ubah array ke string

        $barangMasuk->update($data);

        return redirect()->route('barangmasuk.index')
            ->with('success', 'Data Barang Masuk berhasil diperbarui!');
    }

    /**
     * Hapus data barang masuk.
     */
    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        $barangMasuk->delete();

        return redirect()->route('barangmasuk.index')
            ->with('success', 'Data Barang Masuk berhasil dihapus!');
    }
}
