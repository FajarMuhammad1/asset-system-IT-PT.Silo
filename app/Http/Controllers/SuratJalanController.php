<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use App\Models\MasterBarang;
use App\Models\Ppi; // <--- [PENTING] Tambahin Model PPI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratJalanController extends Controller
{
    /**
     * Tampilkan SEMUA surat jalan
     */
    public function index()
    {
        $suratJalan = SuratJalan::withCount('details')->latest()->get(); 
        
        return view('admin.suratjalan.index', compact('suratJalan'))
                            ->with('title', 'Surat Jalan');
    }

    /**
     * Tampilkan FORM tambah surat jalan
     */
    public function create()
    {
        // 1. Ambil Master Barang (Buat detail items barang keluar)
        $masterBarangList = MasterBarang::all(); 
        
        // 2. [BARU] Ambil Data PPI buat Dropdown
        // Cuma ambil yang statusnya udah di-acc admin (disetujui/selesai)
        $daftarPpi = Ppi::whereIn('status', ['disetujui', 'selesai'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.suratjalan.create', [
            'title' => 'Tambah Surat Jalan',
            'masterBarangList' => $masterBarangList,
            'daftarPpi' => $daftarPpi // <--- Kirim data PPI ke view
        ]);
    }

    /**
     * Simpan data BARU (Header + Details)
     */
    public function store(Request $request)
    {
        // 1. Validasi HEADER
        $request->validate([
            'no_sj' => 'required',
            'id_suratjalan' => 'required|string|unique:surat_jalan,id_suratjalan', 
            'no_ppi' => 'required', // Ini bakal nerima value dari Dropdown
            'no_po' => 'required',
            'tanggal_input' => 'required|date',
            'jenis_surat_jalan' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            
            // 2. Validasi DETAILS
            'items' => 'required|array|min:1',
            'items.*.master_barang_id' => 'required|exists:master_barang,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction(); 

        try {
            // Ambil data header
            $dataHeader = $request->only([
                'no_sj', 'id_suratjalan', 'no_ppi', 'no_po', 'tanggal_input', 'jenis_surat_jalan', 'keterangan'
            ]);
            
            $dataHeader['is_bast_submitted'] = $request->has('is_bast_submitted');

            // Handle file upload
            if ($request->hasFile('file')) {
                $dataHeader['file'] = $request->file('file')->store('surat-jalan', 'public');
            }

            // 1. SIMPAN HEADER
            $suratJalan = SuratJalan::create($dataHeader);

            // 2. SIMPAN DETAIL
            foreach ($request->items as $item) {
                SuratJalanDetail::create([
                    'surat_jalan_id' => $suratJalan->id_sj,
                    'master_barang_id' => $item['master_barang_id'],
                    'qty' => $item['qty'],
                ]);
            }

            DB::commit(); 

            return redirect()->route('surat-jalan.index')
                             ->with('success', 'Surat Jalan baru berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack(); 
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
        $masterBarangList = MasterBarang::all();
        
        // [BARU] Ambil PPI juga buat jaga-jaga kalo mau edit No PPI
        $daftarPpi = Ppi::whereIn('status', ['disetujui', 'selesai'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.suratjalan.edit', [
            'title' => 'Edit Surat Jalan',
            'suratJalan' => $suratJalan,
            'masterBarangList' => $masterBarangList,
            'daftarPpi' => $daftarPpi // <--- Kirim juga ke Edit
        ]);
    }

    /**
     * Update data
     */
    public function update(Request $request, string $id_sj)
    {
        $suratJalan = SuratJalan::findOrFail($id_sj);

        $request->validate([
            'no_sj' => 'required',
            'id_suratjalan' => 'required|string|unique:surat_jalan,id_suratjalan,' . $id_sj . ',id_sj',
            'no_ppi' => 'required',
            'no_po' => 'required',
            'tanggal_input' => 'required|date',
            'jenis_surat_jalan' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);
        
        $dataHeader = $request->only([
            'no_sj', 'id_suratjalan', 'no_ppi', 'no_po', 'tanggal_input', 'jenis_surat_jalan', 'keterangan'
        ]);
        
        $dataHeader['is_bast_submitted'] = $request->has('is_bast_submitted');

        if ($request->hasFile('file')) {
            if ($suratJalan->file) {
                Storage::disk('public')->delete($suratJalan->file);
            }
            $dataHeader['file'] = $request->file('file')->store('surat-jalan', 'public');
        }

        $suratJalan->update($dataHeader);

        return redirect()->route('surat-jalan.index')
                         ->with('success', 'Data Surat Jalan berhasil diperbarui.');
    }

    /**
     * Hapus SJ
     */
    public function destroy(string $id_sj)
    {
        $suratJalan = SuratJalan::findOrFail($id_sj);
        
        if ($suratJalan->file) {
            Storage::disk('public')->delete($suratJalan->file);
        }
        
        $suratJalan->delete();

        return redirect()->route('surat-jalan.index')
                         ->with('success', 'Surat Jalan berhasil dihapus.');
    }
}