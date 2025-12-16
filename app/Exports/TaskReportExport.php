<?php

namespace App\Exports;

use App\Models\TaskReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class TaskReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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

    public function collection()
    {
        // Load relasi 'ticket' dan 'staff'
        $query = TaskReport::with(['staff', 'ticket']);

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

        return $query->latest()->get();
    }

    // --- JUDUL KOLOM DI EXCEL (NO & STATUS DIHAPUS) ---
    public function headings(): array
    {
        return [
            'Tanggal Laporan',      // Kolom 1
            'Nomor Tiket',          // Kolom 2
            'Nama Teknisi',         // Kolom 3
            'Judul Pekerjaan',      // Kolom 4
            'Durasi Pengerjaan',    // Kolom 5
        ];
    }

    // --- ISI DATA PER BARIS ---
    public function map($task): array
    {
        // LOGIC HITUNG DURASI
        $durasi = 'Dalam Proses';
        
        if ($task->tanggal_mulai && $task->tanggal_selesai) {
            $start = Carbon::parse($task->tanggal_mulai);
            $end = Carbon::parse($task->tanggal_selesai);
            
            $totalMinutes = $start->diffInMinutes($end);
            $jam = intdiv($totalMinutes, 60);
            $menit = $totalMinutes % 60;

            $durasi_parts = [];
            if ($jam > 0) $durasi_parts[] = "$jam Jam";
            if ($menit > 0) $durasi_parts[] = "$menit Menit";
            
            if (empty($durasi_parts)) {
                $durasi = "< 1 Menit";
            } else {
                $durasi = implode(" ", $durasi_parts);
            }
        }

        return [
            // No (ID) dihapus
            
            Carbon::parse($task->created_at)->format('d-m-Y H:i'),

            $task->ticket->no_tiket ?? 'Non-Tiket',

            $task->staff->name ?? $task->staff->nama ?? 'Unknown',

            $task->judul ?? $task->pekerjaan ?? '-',

            $durasi,

            // Status dihapus
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}