<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use App\Models\MasterBarang;
use App\Models\Ppi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// --- EXPORT & PDF ---
use App\Exports\SuratJalanExport;       
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SuratJalanController extends Controller
{
    /**
     * Tampilkan SEMUA surat jalan (Halaman Index)
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
        $masterBarangList = MasterBarang::all(); 
        
        $daftarPpi = Ppi::whereIn('status', ['disetujui', 'selesai'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.suratjalan.create', [
            'title' => 'Tambah Surat Jalan',
            'masterBarangList' => $masterBarangList,
            'daftarPpi' => $daftarPpi
        ]);
    }

    /**
     * Simpan data BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_sj' => 'required',
            'id_suratjalan' => 'required|string|unique:surat_jalan,id_suratjalan', 
            'no_ppi' => 'required',
            'no_po' => 'required',
            'tanggal_input' => 'required|date',
            'jenis_surat_jalan' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'items' => 'required|array|min:1',
            'items.*.master_barang_id' => 'required|exists:master_barang,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction(); 

        try {
            $dataHeader = $request->only([
                'no_sj', 'id_suratjalan', 'no_ppi', 'no_po', 'tanggal_input', 'jenis_surat_jalan', 'keterangan'
            ]);
            
            $dataHeader['is_bast_submitted'] = false; 

            if ($request->hasFile('file')) {
                $dataHeader['file'] = $request->file('file')->store('surat-jalan', 'public');
            }

            $suratJalan = SuratJalan::create($dataHeader);

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
     * Tampilkan detail
     */
    public function show(string $id_sj)
    {
        $suratJalan = SuratJalan::with('details.masterBarang')->findOrFail($id_sj);
        
        return view('admin.suratjalan.show', compact('suratJalan'))
                    ->with('title', 'Detail Surat Jalan');
    }

    /**
     * Edit Form
     */
    public function edit(string $id_sj)
    {
        $suratJalan = SuratJalan::with('details.masterBarang')->findOrFail($id_sj);
        $masterBarangList = MasterBarang::all();
        
        $daftarPpi = Ppi::whereIn('status', ['disetujui', 'selesai'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.suratjalan.edit', [
            'title' => 'Edit Surat Jalan',
            'suratJalan' => $suratJalan,
            'masterBarangList' => $masterBarangList,
            'daftarPpi' => $daftarPpi 
        ]);
    }

    /**
     * Update Data
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
     * Hapus Data
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

    // =================================================================
    //  CETAK DOKUMEN (SATUAN) - Untuk tombol "Print" di halaman Detail
    // =================================================================

    public function exportPdf(string $id_sj)
    {
        $suratJalan = SuratJalan::with(['details.masterBarang.kategori'])->findOrFail($id_sj);
        $cleanNoSj = str_replace(['/', '\\'], '-', $suratJalan->no_sj);
        $fileName = 'SJ_Print_' . $cleanNoSj . '.pdf';

        $pdf = Pdf::loadView('exports.surat-jalan-pdf', ['sj' => $suratJalan]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($fileName);
    }


    // =================================================================
    //  HELPER: LOGIKA FILTERING (PRIVATE)
    //  Di sini letak perbaikan Carbon Error
    // =================================================================

    private function getFilteredQueryAndLabel(Request $request)
    {
        // Query awal, eager load details
        $query = SuratJalan::with('details.masterBarang');
        $label = "Semua Data";

        // 1. Filter Harian (Jika user isi tanggal)
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_input', $request->tanggal);
            $label = "Harian Tgl " . Carbon::parse($request->tanggal)->format('d-m-Y');
        } 
        // 2. Filter Bulanan (Jika user pilih Bulan & Tahun)
        elseif ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_input', $request->bulan)
                  ->whereYear('tanggal_input', $request->tahun);
            
            // --- FIX ERROR CARBON ---
            // Gunakan (int) casting dan set tanggal ke 1 untuk menghindari overflow (tgl 31)
            $dateObj = Carbon::createFromDate((int)$request->tahun, (int)$request->bulan, 1);
            $namaBulan = $dateObj->format('F'); // Full month name (January, etc.)
            
            $label = "Bulanan " . $namaBulan . " " . $request->tahun;
        }

        // 3. Filter Status BAST
        if ($request->filled('status_bast')) {
            if ($request->status_bast == 'sudah') {
                $query->where('is_bast_submitted', 1);
                $label .= " (Sudah BAST)";
            } elseif ($request->status_bast == 'belum') {
                $query->where('is_bast_submitted', 0);
                $label .= " (Belum BAST)";
            }
        }

        $query->orderBy('tanggal_input', 'desc');

        return ['query' => $query, 'label' => $label];
    }

    // =================================================================
    //  EXPORT LAPORAN EXCEL (FILTERING MODAL)
    // =================================================================

    public function exportExcelFiltered(Request $request)
    {
        // Panggil helper filter
        $result = $this->getFilteredQueryAndLabel($request);
        
        $data = $result['query']->get();
        $label = $result['label'];
        
        // Nama file
        $cleanLabel = str_replace([' ', '(', ')'], '_', $label);
        $fileName = 'Rekap_SJ_' . $cleanLabel . '_' . date('His') . '.xlsx';
        
        return Excel::download(new SuratJalanExport($data), $fileName);
    }

    // =================================================================
    //  EXPORT LAPORAN PDF (FILTERING MODAL) - REKAP
    // =================================================================

    public function exportPdfFiltered(Request $request)
    {
        // Panggil helper filter yang sama
        $result = $this->getFilteredQueryAndLabel($request);

        $data = $result['query']->get();
        $label = $result['label'];

        // Nama file
        $cleanLabel = str_replace([' ', '(', ')'], '_', $label);
        $fileName = 'Rekap_SJ_' . $cleanLabel . '_' . date('His') . '.pdf';

        // Load View PDF Rekap
        $pdf = Pdf::loadView('admin.suratjalan.surat-jalan-rekap-pdf', [
            'data' => $data,
            'label' => $label,
            'title' => 'Laporan Rekapitulasi Surat Jalan'
        ]);
        
        // Gunakan Landscape karena tabel rekap biasanya lebar
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream($fileName);
    }
}