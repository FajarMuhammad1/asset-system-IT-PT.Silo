<?php

namespace App\Exports;

use App\Models\TaskReport;
use Maatwebsite\Excel\Concerns\FromQuery; // Menggunakan FromQuery untuk performa lebih baik
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
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithCustomStartCell
{
    protected $bulan;
    protected $tahun;
    protected $staff_id;

    public function __construct($bulan, $tahun, $staff_id)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->staff_id = $staff_id;
    }

    /**
     * Data Tabel dimulai dari baris ke-6
     * Baris 1-5 digunakan untuk Kop Laporan Formal
     */
    public function startCell(): string
    {
        return 'A6';
    }

    public function query()
    {
        // Load relasi 'ticket' dan 'staff'
        $query = TaskReport::query()->with(['staff', 'ticket']);

        // Filter Bulan
        if ($this->bulan) {
            $query->whereMonth('created_at', $this->bulan);
        }

        // Filter Tahun
        if ($this->tahun) {
            $query->whereYear('created_at', $this->tahun);
        }

        // Filter Staff
        if ($this->staff_id && $this->staff_id != 'semua') {
            $query->where('staff_id', $this->staff_id);
        }

        return $query->latest();
    }

    // --- JUDUL KOLOM (UPPERCASE UNTUK STANDAR PROFESIONAL) ---
    public function headings(): array
    {
        return [
            'TANGGAL LAPORAN',    // A
            'NOMOR TIKET',        // B
            'NAMA TEKNISI',       // C
            'JUDUL PEKERJAAN',    // D
            'DURASI PENGERJAAN',  // E
        ];
    }

    // --- ISI DATA PER BARIS ---
    public function map($task): array
    {
        // LOGIC HITUNG DURASI (TIDAK DIUBAH, HANYA DIPERAPIH)
        $durasi = 'DALAM PROSES'; // Default Uppercase
        
        if ($task->tanggal_mulai && $task->tanggal_selesai) {
            $start = Carbon::parse($task->tanggal_mulai);
            $end = Carbon::parse($task->tanggal_selesai);
            
            $totalMinutes = $start->diffInMinutes($end);
            $jam = intdiv($totalMinutes, 60);
            $menit = $totalMinutes % 60;

            $durasi_parts = [];
            if ($jam > 0) $durasi_parts[] = "$jam JAM";
            if ($menit > 0) $durasi_parts[] = "$menit MENIT";
            
            if (empty($durasi_parts)) {
                $durasi = "< 1 MENIT";
            } else {
                $durasi = implode(" ", $durasi_parts);
            }
        }

        // Mapping Data (Menggunakan strtoupper untuk standar laporan lapangan)
        return [
            Carbon::parse($task->created_at)->format('d-M-Y H:i'), // Format dd-MMM-yyyy lebih jelas

            strtoupper($task->ticket->no_tiket ?? 'NON-TIKET'),

            strtoupper($task->staff->name ?? $task->staff->nama ?? 'UNKNOWN'),

            strtoupper($task->judul ?? $task->pekerjaan ?? '-'),

            strtoupper($durasi),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling Header Tabel (Baris 6)
        return [
            6 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], // Text Putih
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1F4E78'], // Corporate Navy Blue
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
                
                // Ambil Nama User yg Export
                $userName = 'System';
                if (Auth::check()) {
                    $user = Auth::user();
                    $userName = $user->nama ?? $user->name ?? 'User';
                }

                // --- 1. HEADER JUDUL ---
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'LAPORAN AKTIVITAS HARIAN (DAILY TASK REPORT)');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // --- 2. INFO GENERATED ---
                $sheet->mergeCells('A2:E2');
                $sheet->setCellValue('A2', 'Generated Date: ' . Carbon::now()->format('d F Y, H:i') . ' | By: ' . $userName);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // --- 3. FILTER INFO (AUDIT TRAIL) ---
                // Menggunakan (int) agar Carbon aman membaca bulan
                $monthName = $this->bulan 
                    ? Carbon::createFromDate(null, (int)$this->bulan, 1)->format('F') 
                    : 'All Months';
                $yearName = $this->tahun ?: 'All Years';
                
                $sheet->mergeCells('A3:E3');
                $sheet->setCellValue('A3', "Filter Periode: [ $monthName $yearName ]");
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // --- 4. BORDER & ALIGNMENT DATA ---
                $lastRow = $sheet->getHighestRow();
                
                // Style Border Seluruh Data
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];

                // Terapkan border dari baris 6 sampai bawah
                $sheet->getStyle('A6:E' . $lastRow)->applyFromArray($styleArray);

                // Center Alignment untuk kolom Tanggal (A), Tiket (B), Durasi (E)
                $sheet->getStyle('A6:B'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E6:E'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}