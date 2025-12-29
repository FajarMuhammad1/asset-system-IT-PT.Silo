<?php

namespace App\Exports;

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
use Carbon\Carbon;

class SuratJalanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $data;

    /**
     * CONSTRUCTOR
     * Menerima data yang sudah difilter dari Controller
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * MENGEMBALIKAN DATA
     */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NO SURAT JALAN',
            'NO PPI',
            'NO PO',
            'TANGGAL INPUT',
            'JENIS DOKUMEN',
            'TOTAL ITEM',
            'STATUS BAST',
            'KETERANGAN / CATATAN'
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            strtoupper($row->no_sj),
            strtoupper($row->no_ppi ?? '-'),
            strtoupper($row->no_po ?? '-'),
            Carbon::parse($row->tanggal_input)->format('d-m-Y'),
            strtoupper($row->jenis_surat_jalan),
            $row->details->count(), // Pastikan di controller sudah pakai with('details')
            $row->is_bast_submitted ? 'SUDAH' : 'BELUM', 
            $row->keterangan ?? '-',
        ];
    }

    /**
     * STYLING DASAR (Header Row)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style Baris ke-1 (Header)
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']], // Font Putih
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2C3E50'], // Background Biru Gelap
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * LOGIC EVENT UNTUK STYLING LANJUTAN (Border & Audit Trail)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                // Cari tahu baris terakhir data
                $highestRow = $sheet->getHighestRow();
                $highestColumn = 'I'; // Kolom terakhir kita (sesuai headings)

                // Cek jika data kosong (hanya header), jangan kasih border error
                if ($highestRow < 2) return;

                // 1. SET BORDER UNTUK SELURUH TABEL
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // 2. SET ALIGNMENT TENGAH UNTUK KOLOM TERTENTU
                // Kolom: No(A), Tgl(E), Jml(G), Status(H)
                $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E2:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 3. LOGIC WARNA BARIS (ZEBRA STRIPING)
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 1) { // Baris ganjil dikasih warna abu tipis
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFF2F2F2'],
                            ],
                        ]);
                    }
                }

                // 4. FOOTER / AUDIT TRAIL
                $footerRow = $highestRow + 2;
                $sheet->setCellValue('A' . $footerRow, 'Dicetak pada: ' . Carbon::now()->format('d M Y H:i:s'));
                $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true)->setSize(9);
            },
        ];
    }
}