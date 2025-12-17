<?php

namespace App\Exports;

use App\Models\BarangMasuk;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BarangMasukExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        // PERBAIKAN: Pastikan relasi dipanggil dengan benar
        $query = BarangMasuk::query()
            ->with(['masterBarang', 'suratJalan', 'suratJalan.ppi', 'pemegang']);

        // --- FILTER ---
        if ($this->request->has('kondisi') && $this->request->kondisi != 'semua') {
            $query->where('status', $this->request->kondisi);
        }

        if ($this->request->has('tahun') && $this->request->tahun != '') {
            $query->whereYear('created_at', $this->request->tahun);
        }

        if ($this->request->has('jenis') && $this->request->jenis != 'semua') {
            $query->whereHas('masterBarang', function($q) {
                $q->where('kategori', $this->request->jenis);
            });
        }

        return $query;
    }

    public function map($item): array
    {
        return [
            // 1. Nama Barang
            $item->masterBarang->nama_barang ?? '-', 

            // 2. Kategori
            $item->masterBarang->kategori ?? '-',

            // 3. Merk
            $item->masterBarang->merk ?? '-',

            // 4. Nomor PPI
            $item->suratJalan->ppi->no_ppi ?? $item->suratJalan->no_ppi ?? '-',

            // 5. Nomor PO
            $item->suratJalan->no_po ?? '-',

            // 6. ID Surat Jalan
            $item->suratJalan->id_suratjalan ?? '-',

            // 7. Nomor Surat Jalan
            $item->suratJalan->no_sj ?? '-',

            // 8. Nama Pemegang
            $item->pemegang->name ?? $item->pemegang->nama ?? 'Gudang IT',

            // 9. Departemen
            $item->pemegang->departemen ?? '-',

            // 10. Perusahaan (PERBAIKAN UTAMA DISINI)
            // Cek 'perusahaan' dulu, kalau kosong baru cek 'company'
            $item->pemegang->perusahaan ?? $item->pemegang->company ?? '-', 

            // 11. Status / Kondisi
            $item->status,

            // 12. Kode Aset
            $item->kode_asset ?? 'Consumable',
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Kategori',
            'Merk',
            'Nomor PPI',
            'Nomor PO',
            'ID Surat Jalan',
            'Nomor Surat Jalan',
            'Nama Pemegang',
            'Departemen',
            'Perusahaan',
            'Status / Kondisi',
            'Kode Asset',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}