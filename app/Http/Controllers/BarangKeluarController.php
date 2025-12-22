<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\User;
use App\Models\LogSerahTerima;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; 
use Carbon\Carbon; 
use Illuminate\Support\Str; 

class BarangKeluarController extends Controller
{
    // ... [method create TETAP SAMA] ...

    public function create()
    {
        $asetStok = BarangMasuk::with('masterBarang', 'suratJalan')
            ->where('status', 'Stok')
            ->get();

        $users = User::where('role', '!=', 'admin')->orderBy('nama')->get();

        return view('admin.barangkeluar.create', [
            'title' => 'Form Serah Terima Aset (BAST)',
            'asetStok' => $asetStok,
            'users' => $users,
        ]);
    }

    // --- PERBAIKAN 1: Hapus akses relasi kategori ---
    public function getAssetDetails(Request $request)
    {
        if (!$request->id) {
            return response()->json(['error' => 'ID Aset kosong'], 400);
        }

        try {
            $aset = BarangMasuk::with('masterBarang', 'suratJalan')->findOrFail($request->id);

            if ($aset->status !== 'Stok') {
                return response()->json(['error' => 'Aset tidak berstatus Stok'], 422);
            }

            return response()->json([
                'kode_asset'    => $aset->kode_asset,
                'serial_number' => $aset->serial_number,
                'no_sj'         => $aset->suratJalan->nomor_surat_jalan ?? '-',
                'no_ppi'        => $aset->suratJalan->nomor_ppi ?? '-',
                'no_po'         => $aset->suratJalan->no_po ?? '-',
                
                // PERBAIKAN DI SINI: Langsung ambil kolom kategori, bukan relasi
                'kategori'      => $aset->masterBarang->kategori ?? '-', 
                
                'merk'          => $aset->masterBarang->merk ?? '-',
                'model'         => $aset->masterBarang->nama_barang ?? '-',
                'spesifikasi'   => $aset->masterBarang->spesifikasi ?? '-',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Aset tidak ditemukan: ' . $e->getMessage()], 404);
        }
    }

    // ... [method store, index, show TETAP SAMA] ...
    
    public function store(Request $request) { /* ... kode lama ... */ }
    public function index() { /* ... kode lama ... */ }
    public function show($id) { /* ... kode lama ... */ }

    // --- PERBAIKAN 2: Logic Cetak BAST ---
    public function cetakBast(Request $request, $id)
    {
        // 1. Ambil data
        // HAPUS '.kategori' dari with() karena kategori cuma kolom biasa
        $log = LogSerahTerima::with([
            'aset.masterBarang', // <--- Cukup load masterBarang aja
            'aset.suratJalan',
            'pemegang',
            'admin'
        ])->findOrFail($id);

        // 2. Persiapan Logo
        $path = public_path('image/images.png'); 
        $logoBase64 = null;
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImg = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
        }

        // 3. Logika Penamaan File PDF
        if ($request->has('custom_filename') && !empty($request->custom_filename)) {
            $namaFile = $request->custom_filename;
        } else {
            $kodeRaw = $log->aset->kode_asset; 
            $kodeBersih = preg_replace('/_\d+$/', '', $kodeRaw); 
            $namaFile = str_starts_with($kodeBersih, 'BAST') ? $kodeBersih : 'BAST-' . $kodeBersih;
        }

        // 4. Siapkan data view
        $data = [
            'title' => 'BAST - ' . $namaFile, 
            'log' => $log,
            'logo' => $logoBase64,
            'tanggal_cetak' => Carbon::parse($log->tanggal_serah_terima)->translatedFormat('d F Y'),
            'hari_ini' => Carbon::now()->translatedFormat('d F Y')
        ];

        // 5. LOGIKA PEMILIHAN VIEW BERDASARKAN KATEGORI
        
        // PERBAIKAN DI SINI: Ambil langsung dari kolom, bukan relasi
        $kategoriNama = strtoupper($log->aset->masterBarang->kategori ?? '');

        // Kata kunci untuk Radio
        $keywordRadio = ['RADIO', 'HT', 'RIG', 'KOMUNIKASI', 'WALKIE TALKIE'];
        
        if (Str::contains($kategoriNama, $keywordRadio)) {
            // View Khusus Radio
            $viewName = 'admin.barangkeluar.pdf_bast_radio'; 
        } else {
            // View Default (Laptop/PC)
            $viewName = 'admin.barangkeluar.pdf_bast';
        }

        // 6. Load View & Stream
        $pdf = Pdf::loadView($viewName, $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($namaFile . '.pdf');
    }

    // ... [sisanya TETAP SAMA] ...
}