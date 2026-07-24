<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\NilaiAset;

class AssetReportController extends Controller
{
    /**
     * ===================================================================
     * KONFIGURASI NAMA KOLOM DATABASE ASLI
     * ===================================================================
     * Silakan ubah nilai properti di bawah ini jika nama kolom di tabel 
     * barang_masuk Anda berbeda dengan yang ada di database.
     */
    private $kolomTanggal = 'tanggal_masuk'; // Tgl masuk aset sebagai tgl beli (fallback ke created_at)
    private $kolomHarga   = 'harga';         // Kolom harga (fallback ke relasi nilaiAset.nilai_awal)
    private $kolomKode    = 'kode_asset';    // Kode aset fisik (fallback ke serial_number, lalu kode_masuk/no_sj)

    /**
     * Proses Kueri, Filter, dan Mapping Data Aset
     */
    private function getProcessedAssetData(Request $request)
    {
        // Eager load relasi: nama_barang + kategori ada di masterBarang
        $query = BarangMasuk::with(['masterBarang', 'nilaiAset']);

        // 1. Filter Rentang Tanggal menggunakan kolom asli database
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate($this->kolomTanggal, '>=', $request->start_date)
                  ->whereDate($this->kolomTanggal, '<=', $request->end_date);
        }

        // 2. Filter Status Kondisi (match secara lowercase / flexibel)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Ambil data dengan urutan tanggal terbaru
        $barangRaw = $query->orderBy($this->kolomTanggal, 'desc')->get();

        // 4. MAPPING DATA: Menjembatani kolom asli DB + relasi ke nama variabel Blade
        $assets = $barangRaw->map(function ($item) {
            // Tanggal beli: gunakan kolom utama, fallback ke created_at
            $item->tgl_beli = $item->{$this->kolomTanggal}
                ?? (isset($item->created_at) ? $item->created_at->format('Y-m-d') : null);

            // Harga beli: cek kolom harga, fallback ke nilai_awal dari penilaian terakhir
            $hargaFromColumn = $item->{$this->kolomHarga} ?? null;
            $hargaFromNilaiAset = $item->nilaiAset ? $item->nilaiAset->sortByDesc('tanggal_penilaian')->first()->nilai_awal ?? 0 : 0;
            $item->harga_beli = is_numeric($hargaFromColumn) ? $hargaFromColumn : $hargaFromNilaiAset;

            // Kode aset: fallback berantai sesuai tabel baru (post migration update)
            $item->kode_aset = $item->{$this->kolomKode}
                ?? $item->serial_number
                ?? $item->kode_masuk
                ?? $item->no_sj
                ?? '-';

            // Nama barang: dari relasi masterBarang, fallback ke kolom sendiri (backward compat)
            $item->nama_barang = $item->masterBarang->nama_barang
                ?? $item->nama_barang
                ?? '-';

            // Kategori: dari relasi masterBarang, fallback ke kolom sendiri (backward compat)
            $item->kategori = $item->masterBarang->kategori
                ?? $item->kategori
                ?? '-';

            return $item;
        });

        // 5. Hitung ringkasan data terfilter
        return [
            'assets'     => $assets,
            'totalQty'   => $assets->count(),
            'totalValue' => $assets->sum('harga_beli'),
        ];
    }

    /**
     * Halaman Utama Laporan Nilai Aset
     */
    public function index(Request $request)
    {
        $processed = $this->getProcessedAssetData($request);

        return view('admin.laporan.value_report', [
            'title'          => 'Laporan Nilai Aset Inventaris',
            'assets'         => $processed['assets'],
            'totalQty'       => $processed['totalQty'],
            'totalValue'     => $processed['totalValue'],
            'startDate'      => $request->start_date,
            'endDate'        => $request->end_date,
            'selectedStatus' => $request->status
        ]);
    }

    /**
     * Tampilan Cetak / Print Laporan
     * Menggunakan model NilaiAset agar memiliki Nilai Perolehan (nilai_awal)
     * dan Nilai Buku (nilai_sekarang) sesuai template cetak.
     */
    public function print(Request $request)
    {
        $query = NilaiAset::with('barangMasuk');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('barangMasuk', function ($q) use ($request) {
                $q->whereDate($this->kolomTanggal, '>=', $request->start_date)
                  ->whereDate($this->kolomTanggal, '<=', $request->end_date);
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('barangMasuk', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $assets = $query->orderBy('tanggal_penilaian', 'desc')->get();

        $totalQty          = $assets->count();
        $totalNilaiAwal    = $assets->sum('nilai_awal');
        $totalNilaiSekarang = $assets->sum('nilai_sekarang');

        return view('admin.laporan.value_report_print', [
            'title'            => 'Laporan Nilai Aset Inventaris',
            'assets'           => $assets,
            'totalQty'         => $totalQty,
            'totalNilaiAwal'   => $totalNilaiAwal,
            'totalNilaiSekarang' => $totalNilaiSekarang,
            'startDate'        => $request->start_date,
            'endDate'          => $request->end_date,
            'selectedStatus'   => $request->status,
        ]);
    }
}