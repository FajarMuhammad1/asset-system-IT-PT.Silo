<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RkabBudget;

class RkabController extends Controller
{
    public function index(Request $request)
    {
        $title = "Budget Analysis RKAB";
        $tahunDipilih = $request->get('tahun', date('Y'));
        
        $budgets = RkabBudget::where('tahun', $tahunDipilih)->get();

        $totalAlokasi = $budgets->sum('anggaran_alokasi');
        $totalTerpakai = $budgets->sum('anggaran_terpakai');
        $totalSisa = $totalAlokasi - $totalTerpakai;
        $totalPersentase = $totalAlokasi > 0 ? round(($totalTerpakai / $totalAlokasi) * 100, 2) : 0;

        $listTahun = RkabBudget::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        if($listTahun->isEmpty()) {
            $listTahun = collect([date('Y')]);
        }

        return view('admin.rkab.index', compact(
            'title', 'budgets', 'tahunDipilih', 'listTahun',
            'totalAlokasi', 'totalTerpakai', 'totalSisa', 'totalPersentase'
        ));
    }

    // FUNGSI TAMBAH DATA
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'kategori' => 'required|string|max:255',
            'anggaran_alokasi' => 'required|numeric|min:0',
            'anggaran_terpakai' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        RkabBudget::create([
            'tahun' => $request->tahun,
            'kategori' => $request->kategori,
            'anggaran_alokasi' => $request->anggaran_alokasi,
            'anggaran_terpakai' => $request->anggaran_terpakai ?? 0,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Data Anggaran RKAB berhasil ditambahkan!');
    }

    // FUNGSI EDIT DATA
    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'kategori' => 'required|string|max:255',
            'anggaran_alokasi' => 'required|numeric|min:0',
            'anggaran_terpakai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $budget = RkabBudget::findOrFail($id);
        $budget->update([
            'tahun' => $request->tahun,
            'kategori' => $request->kategori,
            'anggaran_alokasi' => $request->anggaran_alokasi,
            'anggaran_terpakai' => $request->anggaran_terpakai,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Data Anggaran berhasil diperbarui!');
    }

    // FUNGSI HAPUS DATA
    public function destroy($id)
    {
        $budget = RkabBudget::findOrFail($id);
        $budget->delete();

        return redirect()->back()->with('success', 'Data Anggaran berhasil dihapus!');
    }

    // FUNGSI CETAK LAPORAN RKAB
    public function print(Request $request)
    {
        $tahunDipilih = $request->get('tahun', date('Y'));
        $budgets = RkabBudget::where('tahun', $tahunDipilih)->get();

        $totalAlokasi = $budgets->sum('anggaran_alokasi');
        $totalTerpakai = $budgets->sum('anggaran_terpakai');
        $totalSisa = $totalAlokasi - $totalTerpakai;
        $totalPersentase = $totalAlokasi > 0 ? round(($totalTerpakai / $totalAlokasi) * 100, 2) : 0;

        return view('admin.rkab.print', compact(
            'budgets', 'tahunDipilih', 'totalAlokasi', 'totalTerpakai', 'totalSisa', 'totalPersentase'
        ));
    }
}