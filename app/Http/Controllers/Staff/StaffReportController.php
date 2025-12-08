<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaskReport;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StaffReportController extends Controller
{
    /**
     * Halaman List Laporan Saya
     */
    public function index()
    {
        $reports = TaskReport::where('staff_id', Auth::id())
                             ->latest()
                             ->get();

        return view('staff.reports.index', [
            'title' => 'Laporan Pekerjaan Saya',
            'reports' => $reports
        ]);
    }

    /**
     * Form Buat Laporan Baru
     */
    public function create()
    {
        // Ambil tiket yang statusnya 'Closed' milik staff ini
        // Biar bisa dipilih kalau laporannya berdasarkan tiket
        $closedTickets = Ticket::where('teknisi_id', Auth::id())
                               ->where('status', 'Closed')
                               ->latest()
                               ->get();

        return view('staff.reports.create', [
            'title' => 'Buat Laporan Baru',
            'closedTickets' => $closedTickets
        ]);     
    }

    /**
     * Simpan Laporan
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('task_reports', 'public');
        }

        TaskReport::create([
            'staff_id' => Auth::id(),
            'ticket_id' => $request->ticket_id, // Bisa null
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'hasil' => $request->hasil,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lampiran' => $lampiranPath,
        ]);

        return redirect()->route('staff.reports.index')
                         ->with('success', 'Laporan berhasil dibuat!');
    }

    /**
     * Detail Laporan
     */
    public function show($id)
    {
        $report = TaskReport::where('staff_id', Auth::id())->findOrFail($id);

        return view('staff.reports.show', [
            'title' => 'Detail Laporan',
            'report' => $report
        ]);
    }
}