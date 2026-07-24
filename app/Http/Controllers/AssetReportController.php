<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;

class AssetReportController extends Controller
{
    /**
     * ===================================================================
     * KONFIGURASI NAMA KOLOM DATABASE ASLI
     * ===================================================================
     * Silakan ubah nilai properti di bawah ini jika nama kolom di tabel 
     * barang_masuks Anda berbeda dengan yang ada di database.
     */
    private $kolomTanggal = 'created_at';  // Contoh: 'created_at', 'tanggal', 'tgl_masuk'
    private $kolomHarga   = 'harga';       // Contoh: 'harga', 'harga_beli', 'nominal'
    private $kolomKode    = 'kode_masuk';  // Contoh: 'kode_masuk', 'kode_barang', 'kode_aset'

    /**
     * Proses Kueri, Filter, dan Mapping Data Aset
     */
    private function getProcessedAssetData(Request $request)
    {
        $query = BarangMasuk::query();

        // 1. Filter Rentang Tanggal menggunakan kolom asli database
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate($this->kolomTanggal, '>=', $request->start_date)
                  ->whereDate($this->kolomTanggal, '<=', $request->end_date);
        }

        // 2. Filter Status Kondisi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Ambil data dengan urutan tanggal terbaru
        $barangRaw = $query->orderBy($this->kolomTanggal, 'desc')->get();

        // 4. MAPPING DATA: Menjembatani kolom asli DB ke nama variabel yang diminta oleh Blade
        $assets = $barangRaw->map(function ($item) {
            $item->tgl_beli   = $item->{$this->kolomTanggal};
            $item->harga_beli = $item->{$this->kolomHarga} ?? 0;
            $item->kode_aset  = $item->{$this->kolomKode} ?? $item->no_sj ?? '-';
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
     */
    public function print(Request $request)
    {
        $processed = $this->getProcessedAssetData($request);

        return view('admin.laporan.value_report_print', [
            'title'          => 'Laporan Nilai Aset Inventaris',
            'assets'         => $processed['assets'],
            'totalQty'       => $processed['totalQty'],
            'totalValue'     => $processed['totalValue'],
            'startDate'      => $request->start_date,
            'endDate'        => $request->end_date,
            'selectedStatus' => $request->status
        ]);
    }
}