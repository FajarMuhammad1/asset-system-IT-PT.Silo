<?php

namespace App\Exports;

use App\Models\BarangMasuk;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class BarangMasukExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithCustomStartCell
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Memulai data tabel dari baris ke-6
     * Baris 1-5 digunakan untuk Kop/Judul Laporan
     */
    public function startCell(): string
    {
        return 'A6';
    }

    public function query()
    {
        $query = BarangMasuk::query()
            ->with(['masterBarang', 'suratJalan', 'suratJalan.ppi', 'pemegang'])
            ->latest(); // Order by terbaru agar rapi

        // --- FILTER LOGIC ---
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
        // Helper untuk null safety & Uppercase (Standar Laporan Resmi)
        return [
            strtoupper($item->masterBarang->nama_barang ?? '-'),
            strtoupper($item->masterBarang->kategori ?? '-'),
            strtoupper($item->masterBarang->merk ?? '-'),
            
            // Format Tanggal Masuk (Penting untuk Audit)
            Carbon::parse($item->created_at)->format('d-M-Y'),

            $item->suratJalan->ppi->no_ppi ?? $item->suratJalan->no_ppi ?? '-',
            $item->suratJalan->no_po ?? '-',
            $item->suratJalan->id_suratjalan ?? '-',
            $item->suratJalan->no_sj ?? '-',
            
            strtoupper($item->pemegang->name ?? $item->pemegang->nama ?? 'Gudang IT'),
            strtoupper($item->pemegang->departemen ?? '-'),
            strtoupper($item->pemegang->perusahaan ?? $item->pemegang->company ?? '-'),
            
            strtoupper($item->status),
            $item->kode_asset ?? 'CONSUMABLE',
        ];
    }

    public function headings(): array
    {
        return [
            'NAMA BARANG',
            'KATEGORI',
            'MERK / MODEL',
            'TGL TERIMA', // Kolom baru agar informatif
            'NO. PPI',
            'NO. PO',
            'ID SURAT JALAN',
            'NO. SJ FISIK',
            'PEMEGANG (USER)',
            'DEPARTEMEN',
            'PERUSAHAAN (ENTITY)',
            'KONDISI',
            'KODE ASET'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling Header Kolom (Baris 6)
        return [
            6 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], // Teks Putih
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1F4E78'], // Corporate Blue (Standar Tambang)
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // --- 1. SET JUDUL LAPORAN (HEADER ATAS) ---
                $sheet->mergeCells('A1:M1');
                $sheet->setCellValue('A1', 'LAPORAN PENERIMAAN BARANG (INCOMING GOODS REPORT)');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // --- 2. SET INFO CONTEXT (TANGGAL CETAK & FILTER) ---
                $sheet->mergeCells('A2:M2');
                $sheet->setCellValue('A2', 'Generated Date: ' . Carbon::now()->format('d F Y, H:i') . ' | User: ' . (auth()->user()->nama ?? 'System'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Info Filter yang dipakai (Audit Trail)
                $filterText = "Filter Applied: ";
                $filterText .= "Tahun: " . ($this->request->tahun ?: 'All') . " | ";
                $filterText .= "Kondisi: " . ($this->request->kondisi ?: 'All') . " | ";
                $filterText .= "Jenis: " . ($this->request->jenis ?: 'All');
                
                $sheet->mergeCells('A3:M3');
                $sheet->setCellValue('A3', $filterText);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // --- 3. BORDER TABLE DATA ---
                // Hitung baris terakhir data
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                
                // Terapkan Border Hitam Tipis ke Seluruh Data (Mulai baris 6 sampai akhir)
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM, // Garis luar lebih tebal
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];

                $sheet->getStyle('A6:' . $lastColumn . $lastRow)->applyFromArray($styleArray);

                // --- 4. ALIGNMENT KHUSUS ---
                // Center align untuk kolom-kolom pendek (Tgl, Kondisi, Kode Aset)
                // Sesuaikan kolom D (Tgl), L (Kondisi), M (Kode Aset) jika indeks berubah
                $sheet->getStyle('D6:D'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tgl
                $sheet->getStyle('L6:M'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status & Asset
            },
        ];
    }
}