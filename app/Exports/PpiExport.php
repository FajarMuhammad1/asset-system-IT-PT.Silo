<?php

namespace App\Exports;

use App\Models\Ppi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PpiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $bulan;
    protected $tahun;
    protected $divisi;

    // 1. TERIMA INPUT DARI CONTROLLER
    public function __construct($bulan, $tahun, $divisi)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->divisi = $divisi;
    }

    // 2. QUERY DATA DENGAN FILTER
    public function collection()
    {
        // Mulai Query dengan Eager Loading User
        $query = Ppi::with('user');

        // Filter Bulan
        if ($this->bulan) {
            $query->whereMonth('created_at', $this->bulan);
        }

        // Filter Tahun
        if ($this->tahun) {
            $query->whereYear('created_at', $this->tahun);
        }

        // Filter Divisi/Departemen
        // KARENA DIVISI ADA DI TABEL USERS, KITA PAKAI whereHas
        if ($this->divisi && $this->divisi != 'semua') {
            $query->whereHas('user', function($q) {
                // Pastikan nama kolom di tabel 'users' adalah 'departemen'
                $q->where('departemen', $this->divisi);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            
            'No PPI',
            'Tanggal Dibuat',
            'User Request',      
            'Departemen',        
            'Deskripsi Masalah',
            'Status',
        ];
    }

    public function map($ppi): array
    {
        return [
          
            $ppi->no_ppi,
            // Format Tanggal
            \Carbon\Carbon::parse($ppi->created_at)->format('d-m-Y'), 
            
            // Nama User
            $ppi->user->name ?? $ppi->user->nama ?? 'User Terhapus',

            // Departemen User
            $ppi->user->departemen ?? '-',        

            // Keluhan/Deskripsi
            $ppi->keluhan ?? $ppi->deskripsi ?? '-',

            ucfirst($ppi->status), 
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}