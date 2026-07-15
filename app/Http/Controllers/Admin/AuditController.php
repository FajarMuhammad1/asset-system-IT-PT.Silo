<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Audit;
use App\Models\AuditItem;
use App\Models\BarangMasuk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF; // Jika menggunakan DomPDF versi baru, disarankan pakai: use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuditController extends Controller
{
    /**
     * Menampilkan daftar sesi audit.
     */
    public function index(): View
    {
        $title = "Daftar Audit Aset";
        // Eager load 'pengaju' untuk menampilkan siapa yang membuat sesi
        $audits = Audit::with(['pengaju'])->latest()->get();
        return view('admin.audit.index', compact('title', 'audits'));
    }

    /**
     * Membuka sesi audit baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Audit::create([
            'title' => $request->title,
            'audit_date' => Carbon::today(),
            'status' => 'On Progress',
            'created_by' => Auth::id(),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Sesi Audit berhasil dibuka.');
    }

    /**
     * Halaman untuk melakukan Opname (Scan item).
     */
    public function show(int $id): View
    {
        // Mengoptimalkan eager loading
        $audit = Audit::with([
            'pengaju',
            'items' => function ($query) {
                $query->latest('scanned_at'); // Urutkan item discan terbaru di atas
            },
            'items.aset.masterBarang',
            'items.scanner'
        ])->findOrFail($id);

        $title = "Opname: " . $audit->title;

        // Hanya hitung aset yang benar-benar ditemukan/di-scan
        $totalScanned = $audit->items->where('is_found', true)->count();

        // Logika dinamis untuk menghitung aset "Hilang / Belum di-scan"
        if ($audit->status === 'Completed') {
            // Jika sudah selesai, hitung dari data yang ditandai is_found = false
            $missingCount = $audit->items->where('is_found', false)->count();
        } else {
            // Jika masih On Progress, cari selisih total aset di DB dengan yang sudah discan
            $totalActiveAssets = BarangMasuk::whereNotIn('status', ['Dimusnahkan', 'Hilang', 'Scrapped'])->count();
            $missingCount = $totalActiveAssets - $totalScanned;
            // Cegah angka minus jika terjadi inkonsistensi data
            $missingCount = $missingCount < 0 ? 0 : $missingCount; 
        }

        $summary = [
            'total_scanned' => $totalScanned,
            'match'         => $audit->items->where('is_match', true)->where('is_found', true)->count(),
            'mismatch'      => $audit->items->where('is_match', false)->where('is_found', true)->count(),
            'missing'       => $missingCount,
        ];

        return view('admin.audit.show', compact('title', 'audit', 'summary'));
    }

    /**
     * Logika API/Ajax saat Barcode discan.
     */
    public function scanItem(Request $request, int $auditId): JsonResponse
    {
        $request->validate([
            'kode_asset' => 'required|exists:barang_masuk,kode_asset',
            'condition' => 'required|in:Good,Damaged',
            'current_location' => 'nullable|string', // Lokasi fisik saat ditemukan
        ]);

        $audit = Audit::findOrFail($auditId);
        
        if ($audit->status !== 'On Progress') {
            return response()->json(['error' => 'Sesi audit ini sudah ditutup.'], 422);
        }

        $aset = BarangMasuk::where('kode_asset', $request->kode_asset)->first();

        // Cek apakah aset ini sudah discan di sesi audit ini?
        $existingEntry = AuditItem::where('audit_id', $auditId)
            ->where('barang_masuk_id', $aset->id)
            ->first();
            
        if ($existingEntry) {
            return response()->json(['error' => 'Aset ini sudah discan sebelumnya pada sesi ini.'], 422);
        }

        // LOGIKA PERBANDINGAN (Discrepancy) - Dengan perlindungan nilai Null
        $dbLocation = $aset->lokasi_saat_ini ?? '';
        $scannedLocation = $request->current_location ?? '';

        // Normalisasi string untuk perbandingan
        $isMatch = (trim(strtolower($dbLocation)) === trim(strtolower($scannedLocation)));

        // Buat data item audit
        AuditItem::create([
            'audit_id' => $auditId,
            'barang_masuk_id' => $aset->id,
            'scanned_location' => $request->current_location,
            'condition' => $request->condition,
            'is_match' => $isMatch,
            'is_found' => true,
            'scanned_by' => Auth::id(),
            'scanned_at' => now(),
        ]);

        return response()->json([
            'success' => 'Aset berhasil dicatat.',
            'data' => [
                'kode' => $aset->kode_asset,
                'nama' => $aset->masterBarang->nama_barang ?? 'Unknown',
                'is_match' => $isMatch,
                'condition' => $request->condition
            ]
        ]);
    }

    /**
     * Selesai Audit & Tutup Sesi (Optimized with Transaction and Mass Updates).
     */
    public function complete(int $id): RedirectResponse
    {
        $audit = Audit::findOrFail($id);

        if ($audit->status !== 'On Progress') {
            return back()->with('error', 'Audit tidak sedang berjalan.');
        }

        // PERBAIKAN: Gunakan Subquery agar memori lebih hemat, dibandingkan menarik semua ID ke array PHP
        $scannedAssetIdsQuery = AuditItem::where('audit_id', $id)->select('barang_masuk_id');

        // Ambil aset yang seharusnya ada tapi tidak ada di daftar scan
        $unscannedAssets = BarangMasuk::whereNotIn('id', $scannedAssetIdsQuery)
            ->whereNotIn('status', ['Dimusnahkan', 'Hilang', 'Scrapped'])
            ->get();

        // JIKA tidak ada aset yang hilang, langsung tutup sesi
        if ($unscannedAssets->isEmpty()) {
            $audit->update([
                'status' => 'Completed',
                'end_date' => Carbon::today()
            ]);
            return redirect()->route('admin.audit.show', $id)->with('success', 'Audit selesai. Semua aset lengkap.');
        }

        // Persiapan data untuk Mass Insert dan Mass Update
        $auditItemsData = [];
        $missingAssetIds = [];
        $now = now();

        foreach ($unscannedAssets as $asset) {
            // Data untuk mass insert ke audit_items
            $auditItemsData[] = [
                'audit_id' => $id,
                'barang_masuk_id' => $asset->id,
                'is_found' => false,
                'is_match' => false, // Dianggap tidak match karena tidak ditemukan
                'condition' => 'Missing',
                'scanned_location' => null,
                'scanned_by' => null, // Sistem yang mencatat
                'scanned_at' => $now, 
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Kumpulkan ID untuk mass update ke barang_masuk
            $missingAssetIds[] = $asset->id;
        }

        // DATABASE TRANSACTION: Menjamin konsistensi data
        try {
            DB::transaction(function () use ($audit, $auditItemsData, $missingAssetIds) {
                // Mass Catat sebagai 'Missing' di AuditItem
                // Chunk insert jika datanya sangat besar untuk menghindari limit param SQL
                foreach (array_chunk($auditItemsData, 500) as $chunk) {
                    AuditItem::insert($chunk);
                }

                // Mass Update status barang utama jadi Hilang
                BarangMasuk::whereIn('id', $missingAssetIds)->update(['status' => 'Hilang']);

                // Update status sesi audit menjadi Completed
                $audit->update([
                    'status' => 'Completed',
                    'end_date' => Carbon::today()
                ]);
            });
        } catch (\Exception $e) {
            // Jika gagal, sistem akan otomatis rollback
            return back()->with('error', 'Terjadi kesalahan sistem saat menutup audit: ' . $e->getMessage());
        }

        // Kembalikan ke halaman show agar user bisa langsung mencetak PDF
        return redirect()->route('admin.audit.show', $id)->with('success', 'Audit selesai. Sesi ditutup dan aset yang tidak ditemukan telah ditandai hilang.');
    }

    /**
     * Menghapus sesi audit beserta isinya.
     */
    public function destroy(int $id): RedirectResponse
    {
        $audit = Audit::findOrFail($id);
        
        // Hapus manual relasinya terlebih dahulu (jika onDelete Cascade tidak diset di database)
        $audit->items()->delete(); 
        
        $audit->delete();

        return redirect()->route('admin.audit.index')->with('success', 'Sesi Audit berhasil dihapus.');
    }

    /**
     * Menggenerasi Laporan PDF untuk hasil audit.
     */
    public function printReport(int $id)
    {
        // Eager load data lengkap untuk laporan
        $audit = Audit::with([
            'pengaju',
            'items.aset.masterBarang',
            'items.scanner'
        ])->findOrFail($id);

        // Validasi: Laporan hanya bisa dicetak jika sudah selesai
        if ($audit->status !== 'Completed') {
            return back()->with('error', 'Laporan hanya dapat dicetak untuk audit yang telah selesai.');
        }

        // Hitung statistik akhir untuk header laporan
        $items = $audit->items;
        $found = $items->where('is_found', true);

        // Array summary disesuaikan agar cocok dengan key di report_pdf.blade.php
        $summary = [
            'total_db'      => $items->count(),
            'total_scanned' => $found->count(),
            'match'         => $found->where('is_match', true)->count(),
            'mismatch'      => $found->where('is_match', false)->count(),
            'damaged'       => $found->where('condition', 'Damaged')->count(),
            'missing'       => $items->where('is_found', false)->count(),
        ];

        $title = "Laporan Hasil Audit: " . $audit->title;

        // Siapkan view PDF
        $pdf = PDF::loadView('admin.audit.report_pdf', compact('title', 'audit', 'summary'));

        // Atur kertas
        $pdf->setPaper('a4', 'portrait');

        // Penanganan tanggal untuk filename (mencegah error format on null)
        $tanggalFile = $audit->end_date 
            ? Carbon::parse($audit->end_date)->format('Ymd') 
            : now()->format('Ymd');

        // Download file
        return $pdf->download('Audit_Report_' . str_replace(' ', '_', $audit->title) . '_' . $tanggalFile . '.pdf');
    }
}