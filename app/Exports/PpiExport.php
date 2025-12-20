<?php

namespace App\Exports;

use App\Models\Ppi;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents; 
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PpiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $tanggal;
    protected $bulan;
    protected $tahun;
    protected $perusahaan;
    protected $results;

    public function __construct($tanggal = null, $bulan = null, $tahun = null, $perusahaan = null)
    {
        $this->tanggal = $tanggal;
        $this->bulan   = $bulan;
        $this->tahun   = $tahun;
        $this->perusahaan = $perusahaan;
    }

    public function collection()
    {
        $query = Ppi::with('user');

        if ($this->tanggal) {
            $query->whereDate('created_at', $this->tanggal);
        } else {
            if ($this->bulan) $query->whereMonth('created_at', $this->bulan);
            if ($this->tahun) $query->whereYear('created_at', $this->tahun);
        }

        if ($this->perusahaan && $this->perusahaan != 'semua') {
            $query->whereHas('user', function($q) {
                $q->where('perusahaan', $this->perusahaan);
            });
        }

        $this->results = $query->get();
        return $this->results;
    }

    public function headings(): array
    {
        // Ambil tahun dari filter, atau tahun sekarang jika kosong
        $tahunLaporan = $this->tahun ? $this->tahun : date('Y');
        $judulAtas = "LAPORAN REKAPITULASI PPI - PERIODE TAHUN $tahunLaporan";

        return [
            // BARIS 1: JUDUL BESAR
            [$judulAtas], 

            // BARIS 2: HEADER TABEL
            [
                'No PPI',
                'Tanggal Dibuat',
                'User Request',      
                'Perusahaan',       
                'Deskripsi Masalah',
                'Status',
            ]
        ];
    }

    public function map($ppi): array
    {
        $deskripsi = $ppi->deskripsi_masalah ?? $ppi->masalah ?? $ppi->keluhan ?? $ppi->deskripsi ?? $ppi->keterangan ?? '-';

        return [
            $ppi->no_ppi,
            \Carbon\Carbon::parse($ppi->created_at)->format('d-m-Y'), 
            $ppi->user->name ?? $ppi->user->nama ?? 'User Terhapus',
            $ppi->user->perusahaan ?? '-',        
            strip_tags($deskripsi),
            ucfirst($ppi->status), 
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style Baris 1 (JUDUL BESAR)
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],

            // Style Baris 2 (HEADER TABEL - Sebelumnya Baris 1)
            2 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], // Teks Putih
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4B5563'], // Background Abu Gelap
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],

            // Kolom E (Deskripsi) Wrap Text
            'E' => ['alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_TOP]],
            
            // Kolom A, B, F Center Alignment (Mulai dari baris data)
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_TOP]],
            'B' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_TOP]],
            'F' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_TOP]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $data = $this->results;
                
                // Merge Cells untuk Judul (A1 sampai F1)
                $sheet->mergeCells('A1:F1');
                
                // Tinggi Baris Judul biar agak lega
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Hitung baris terakhir data (Data + 2 baris header)
                $lastRow = $data->count() + 2; 

                // 1. Beri Border pada Tabel Utama (Mulai dari A2 sampai data habis)
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];
                // Terapkan border dari baris 2 (Header Tabel) ke bawah
                $sheet->getStyle('A2:F' . $lastRow)->applyFromArray($styleArray);

                // --- LOGIK STATISTIK (Sama seperti sebelumnya) ---
                $statsPerusahaan = $data->groupBy(fn($i) => $i->user->perusahaan ?? 'Tanpa Perusahaan')->map->count()->sortDesc();
                $statsDepartemen = $data->groupBy(fn($i) => $i->user->departemen ?? 'Tanpa Departemen')->map->count()->sortDesc();
                $statsUser = $data->groupBy(fn($i) => $i->user->nama ?? $i->user->name ?? 'Unknown')->map->count()->sortDesc();
                $statsStatus = $data->groupBy('status')->map->count();
                $grandTotal = $data->count();

                $currentRow = $sheet->getHighestRow() + 3;

                // Fungsi Helper untuk Membuat Tabel Kecil
                $createTable = function($title, $header1, $header2, $statsData) use ($sheet, &$currentRow) {
                    $sheet->setCellValue('A' . $currentRow, $title);
                    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);
                    $currentRow++;

                    $startRow = $currentRow;
                    $sheet->setCellValue('A' . $currentRow, $header1);
                    $sheet->setCellValue('B' . $currentRow, $header2);
                    
                    $sheet->getStyle("A$currentRow:B$currentRow")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFD1D5DB'], 
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $currentRow++;

                    foreach ($statsData as $key => $val) {
                        $sheet->setCellValue('A' . $currentRow, $key);
                        $sheet->setCellValue('B' . $currentRow, $val);
                        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $currentRow++;
                    }

                    $endRow = $currentRow - 1;
                    $sheet->getStyle("A$startRow:B$endRow")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                    ]);
                    
                    $currentRow++; 
                };

                $createTable('REKAPITULASI BY PERUSAHAAN', 'Nama Perusahaan', 'Total', $statsPerusahaan);
                $createTable('REKAPITULASI BY DEPARTEMEN', 'Nama Departemen', 'Total', $statsDepartemen);
                $createTable('REKAPITULASI BY USER (TOP REQUESTER)', 'Nama User', 'Total Request', $statsUser);
                
                // Manual untuk Status & Grand Total
                $sheet->setCellValue('A' . $currentRow, 'REKAPITULASI STATUS & TOTAL');
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);
                $currentRow++;

                $startRow = $currentRow;
                $sheet->setCellValue('A' . $currentRow, 'Status PPI');
                $sheet->setCellValue('B' . $currentRow, 'Jumlah');
                
                $sheet->getStyle("A$currentRow:B$currentRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1D5DB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $currentRow++;

                foreach ($statsStatus as $status => $total) {
                    $sheet->setCellValue('A' . $currentRow, ucfirst($status));
                    $sheet->setCellValue('B' . $currentRow, $total);
                    $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $currentRow++;
                }

                $sheet->setCellValue('A' . $currentRow, 'GRAND TOTAL');
                $sheet->setCellValue('B' . $currentRow, $grandTotal);
                
                $sheet->getStyle("A$currentRow:B$currentRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFEF08A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle("A$startRow:B$currentRow")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}