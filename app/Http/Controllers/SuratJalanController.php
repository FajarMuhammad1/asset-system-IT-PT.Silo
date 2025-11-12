<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use App\Models\MasterBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // <-- Panggil Storage buat hapus file

class SuratJalanController extends Controller
{
    /**
     * Tampilkan SEMUA surat jalan
     */
    public function index()
    {
        // 'withCount' buat ngitung ada berapa JENIS barang di dalemnya
        $suratJalan = SuratJalan::withCount('details')->latest()->get(); 
        
        return view('admin.suratjalan.index', compact('suratJalan'))
                             ->with('title', 'Surat Jalan');
    }

    /**
     * Tampilkan FORM tambah surat jalan
     */
    public function create()
    {
        // Lo HARUS ngirim 'master_barang' (Katalog) ke view,
        // buat ngisi dropdown "Pilih Barang"
        $masterBarangList = MasterBarang::all(); 
        
        return view('admin.suratjalan.create', [
            'title' => 'Tambah Surat Jalan',
            'masterBarangList' => $masterBarangList // <-- KIRIM KE VIEW
        ]);
    }

    /**
     * Simpan data BARU (Header + Details)
     */
    public function store(Request $request)
    {
        // 1. Validasi HEADER (Info Dokumen)
        $request->validate([
            // no_sj tidak unik lagi, tapi harus diisi
            'no_sj' => 'required',
            
            // KOLOM BARU LO: id_suratjalan (WAJIB UNIK)
            'id_suratjalan' => 'required|string|unique:surat_jalan,id_suratjalan', 
            
            'no_ppi' => 'required',
            'no_po' => 'required',
            'tanggal_input' => 'required|date',
            'jenis_surat_jalan' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            
            // 2. Validasi DETAILS (List Barangnya)
            'items' => 'required|array|min:1',
            'items.*.master_barang_id' => 'required|exists:master_barang,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction(); 

        try {
            // Ambil data header (TAMBAH 'id_suratjalan')
            $dataHeader = $request->only([
                'no_sj', 'id_suratjalan', 'no_ppi', 'no_po', 'tanggal_input', 'jenis_surat_jalan', 'keterangan'
            ]);
            
            // Kalo checkbox dicentang, $request->has() = true
            $dataHeader['is_bast_submitted'] = $request->has('is_bast_submitted');

            // Handle file upload
            if ($request->hasFile('file')) {
                // Simpan file di storage/app/public/surat-jalan
                $dataHeader['file'] = $request->file('file')->store('surat-jalan', 'public');
            }

            // 1. SIMPAN HEADER DULU
            $suratJalan = SuratJalan::create($dataHeader);

            // 2. SIMPAN DETAILNYA (LOOPING)
            foreach ($request->items as $item) {
                SuratJalanDetail::create([
                    'surat_jalan_id' => $suratJalan->id_sj, // <-- Link ke header (PK int)
                    'master_barang_id' => $item['master_barang_id'],
                    'qty' => $item['qty'],
                ]);
            }

            DB::commit(); 

            return redirect()->route('surat-jalan.index')
                             ->with('success', 'Surat Jalan baru berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack(); 
            // Tambah logika hapus file kalo transaction gagal
            if (isset($dataHeader['file'])) {
                Storage::disk('public')->delete($dataHeader['file']);
            }
            return back()->withInput()->with('error', 'Gagal menyimpan! Error: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail (Read-only)
     */
    public function show(string $id_sj)
    {
        // Ambil SJ + List Detailnya + Info Master Barangnya
        $suratJalan = SuratJalan::with('details.masterBarang')->findOrFail($id_sj);
        
        return view('admin.suratjalan.show', compact('suratJalan'))
                             ->with('title', 'Detail Surat Jalan');
    }

    /**
     * Tampilkan form EDIT
     */
    public function edit(string $id_sj)
    {
        $suratJalan = SuratJalan::with('details.masterBarang')->findOrFail($id_sj);
        $masterBarangList = MasterBarang::all(); // (Daftar barang buat nambah)
        
        return view('admin.suratjalan.edit', [
            'title' => 'Edit Surat Jalan',
            'suratJalan' => $suratJalan,
            'masterBarangList' => $masterBarangList
        ]);
    }

    /**
     * Update data
     */
    public function update(Request $request, string $id_sj)
    {
        $suratJalan = SuratJalan::findOrFail($id_sj);

        $request->validate([
            // no_sj tidak unik lagi
            'no_sj' => 'required',
            
            // KOLOM BARU: id_suratjalan (WAJIB UNIK, kecualo ID sendiri)
            'id_suratjalan' => 'required|string|unique:surat_jalan,id_suratjalan,' . $id_sj . ',id_sj',
            
            'no_ppi' => 'required',
            'no_po' => 'required',
            'tanggal_input' => 'required|date',
            'jenis_surat_jalan' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            
            // Update details sebaiknya di-handle di route/method lain (misal: PUT /details/{id})
        ]);
        
        $dataHeader = $request->only([
            'no_sj', 'id_suratjalan', 'no_ppi', 'no_po', 'tanggal_input', 'jenis_surat_jalan', 'keterangan'
        ]);
        
        $dataHeader['is_bast_submitted'] = $request->has('is_bast_submitted');

        if ($request->hasFile('file')) {
            // --- LOGIKA HAPUS FILE LAMA (PENTING) ---
            if ($suratJalan->file) {
                Storage::disk('public')->delete($suratJalan->file);
            }
            // ----------------------------------------
            
            $dataHeader['file'] = $request->file('file')->store('surat-jalan', 'public');
        }

        $suratJalan->update($dataHeader);

        return redirect()->route('surat-jalan.index')
                         ->with('success', 'Data Surat Jalan berhasil diperbarui.');
    }

    /**
     * Hapus SJ (dan detailnya otomatis kehapus)
     */
    public function destroy(string $id_sj)
    {
        $suratJalan = SuratJalan::findOrFail($id_sj);
        
        // --- LOGIKA HAPUS FILE YANG DISIMPAN ---
        if ($suratJalan->file) {
            Storage::disk('public')->delete($suratJalan->file);
        }
        // ----------------------------------------
        
        $suratJalan->delete(); // 'onDelete(cascade)' di migrasi bakal ngehapus 'details'-nya

        return redirect()->route('surat-jalan.index')
                         ->with('success', 'Surat Jalan berhasil dihapus.');
    }
}