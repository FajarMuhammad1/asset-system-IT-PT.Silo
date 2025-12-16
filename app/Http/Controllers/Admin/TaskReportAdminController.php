<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskReport;
use Illuminate\Http\Request;             // <--- WAJIB ADA
use App\Exports\TaskReportExport;        // <--- CLASS EXPORT BARU
use Maatwebsite\Excel\Facades\Excel;     // <--- LIBRARY EXCEL

class TaskReportAdminController extends Controller
{
    // LIST SEMUA TASK REPORT
    public function index()
    {
        $reports = TaskReport::with('staff', 'ticket')
            ->latest()
            ->get();

        return view('admin.taskreport.index', [
            'title' => 'Laporan Task Staff',
            'reports' => $reports
        ]);
    }

    // DETAIL TASK REPORT
    public function show($id)
    {
        $report = TaskReport::with('staff', 'ticket')->findOrFail($id);

        return view('admin.taskreport.show', [
            'title' => 'Detail Task Report Staff',
            'report' => $report
        ]);
    }

    // --- FITUR BARU: EXPORT EXCEL ---
    public function exportExcel(Request $request)
    {
        // 1. Tangkap Filter dari Modal
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $staff_id = $request->input('staff_id');

        // 2. Buat Nama File Unik (biar gak ketuker)
        $nama_file = 'Laporan-Kinerja-IT-' . date('d-m-Y_H-i-s') . '.xlsx';

        // 3. Download Excel
        // Kita kirim parameter filter ke Class Export
        return Excel::download(new TaskReportExport($bulan, $tahun, $staff_id), $nama_file);
    }
}