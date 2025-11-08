<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use Illuminate\Http\Request;

class SuratJalanController extends Controller
{
    public function index()
    {
        $suratJalan = SuratJalan::all();
        return view('admin.suratjalan.index', compact('suratJalan'))
               ->with('title', 'Surat Jalan');
    }

    public function create()
    {
        return view('admin.suratjalan.create', [
            'title' => 'Tambah Surat Jalan'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_sj' => 'required|unique:surat_jalan,no_sj',
            'no_ppi' => 'required',
            'no_po' => 'required',
            'kategori' => 'required',
            'merk' => 'required',
            'model' => 'required',
            'qty' => 'required|integer',
            'tanggal_input' => 'required|date',
        ]);

        $data = $request->all();

        if (is_array($data['kategori'])) {
            $data['kategori'] = implode(', ', $data['kategori']);
        }

        if (isset($data['bast']) && is_array($data['bast'])) {
            $data['bast'] = implode(', ', $data['bast']);
        }

        SuratJalan::create($data);

        return redirect()->route('surat-jalan.index')->with('success', 'Surat Jalan berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $suratJalan = SuratJalan::findOrFail($id);
        return view('admin.suratjalan.show', compact('suratJalan'))
               ->with('title', 'Detail Surat Jalan');
    }

    public function edit(string $id)
    {
        $suratJalan = SuratJalan::findOrFail($id);
        return view('admin.suratjalan.edit', [
            'title' => 'Edit Surat Jalan',
            'suratJalan' => $suratJalan
        ]);
    }

public function update(Request $request, string $id)
{
    $suratJalan = SuratJalan::findOrFail($id);

    $request->validate([
        'no_sj' => 'required|unique:surat_jalan,no_sj,' . $id . ',id_sj',
        'no_ppi' => 'required',
        'no_po' => 'required',
        'kategori' => 'required',
        'merk' => 'required',
        'model' => 'required',
        'spesifikasi' => 'nullable',
        'serial_number' => 'nullable',
        'qty' => 'required|numeric',
        'keterangan' => 'nullable',
        'jenis_surat_jalan' => 'required',
        'tanggal_input' => 'required|date',
        'kode_asset' => 'nullable',
        'bast' => 'nullable',
        'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:4096', // tambahkan validasi file
    ]);

    $data = $request->all();

    // Kalau kategori berupa array, gabungkan pakai koma
    if (is_array($request->kategori)) {
        $data['kategori'] = implode(', ', $request->kategori);
    }

    // Handle upload file jika ada
    if ($request->hasFile('file')) {
        // Hapus file lama kalau ada
        if ($suratJalan->file && file_exists(storage_path('app/public/' . $suratJalan->file))) {
            unlink(storage_path('app/public/' . $suratJalan->file));
        }

        // Simpan file baru ke folder 'surat-jalan' di storage/public
        $data['file'] = $request->file('file')->store('surat-jalan', 'public');
    }

    $suratJalan->update($data);

    return redirect()->route('surat-jalan.index')
                     ->with('success', 'Data Surat Jalan berhasil diperbarui.');
}


    public function destroy(string $id)
    {
        $suratJalan = SuratJalan::findOrFail($id);
        $suratJalan->delete();

        return redirect()->route('surat-jalan.index')
                         ->with('success', 'Surat Jalan berhasil dihapus.');
    }
}
