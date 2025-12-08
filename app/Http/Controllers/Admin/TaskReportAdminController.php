<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskReport;

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
}
