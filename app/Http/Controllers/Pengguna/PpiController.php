<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ppi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\File; // Tambahan untuk cek folder

class PpiController extends Controller
{
    public function create()
    {
        return view('pengguna.ppi.create', [
            'title' => 'Buat Pengajuan PPI',
            'menuPPI' => 'active',
            // Kirim nomor sementara/kosong, nanti di-generate real-nya saat store
            'nomor_otomatis' => '(Otomatis setelah Simpan)' 
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'perangkat'    => 'required|string|max:255',
            'ba_kerusakan' => 'required|string', // Pastikan di form name-nya deskripsi atau ba_kerusakan disesuaikan
            'keterangan'   => 'nullable|string',
            'file_ppi'     => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'ttd_pemohon'  => 'required', // Wajib Tanda Tangan
        ]);

        // Ambil user
        $user = Auth::user();

        // ==========================================================
        // 2. LOGIKA GENERATE NOMOR (KODE PERUSAHAAN + ROMAWI)
        // ==========================================================
        $now = Carbon::now();
        $tahun = $now->format('Y');
        $bulanAngka = $now->format('n');

        $romanMonths = [
            1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',
            7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'
        ];
        $bulanRoman = $romanMonths[$bulanAngka];

        // Logika Nama Perusahaan
        $raw = strtoupper(trim($user->perusahaan ?? ''));
        if (str_contains($raw, 'PT')) {
            $parts = preg_split('/[ .]+/', $raw);
            $cleanParts = array_filter($parts, fn($p) => $p !== 'PT');
            $kodePerusahaan = implode(' ', $cleanParts);
        } else {
            $kodePerusahaan = $raw;
        }
        if (!$kodePerusahaan) { $kodePerusahaan = "CORP"; }

        // Pola generate nomor
        $pattern = ".PPI-{$kodePerusahaan}.{$bulanRoman}.{$tahun}";

        // Ambil nomor terakhir
        $lastData = Ppi::where('no_ppi', 'like', "%{$pattern}")->latest('id')->first();
        $urut = 1;

        if ($lastData) {
            $parts = explode('.', $lastData->no_ppi);
            $lastUrut = (int) $parts[0];
            $urut = $lastUrut + 1;
        }

        $nomorUrut = str_pad($urut, 4, '0', STR_PAD_LEFT);
        $nomerOtomatis = $nomorUrut . $pattern;

        // ==========================================================
        // 3. LOGIKA SIMPAN TANDA TANGAN (BASE64 -> IMAGE)
        // ==========================================================
        $ttdPath = null;
        if ($request->ttd_pemohon) {
            // Decode Base64
            $image_parts = explode(";base64,", $request->ttd_pemohon);
            // Cek validitas base64
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                
                // Nama File Unik
                $fileName = 'ttd_ppi_' . uniqid() . '.png';
                $folderPath = public_path('uploads/signatures/');
                
                // Buat folder jika belum ada
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0777, true, true);
                }

                // Simpan File
                file_put_contents($folderPath . $fileName, $image_base64);
                
                // Set path untuk database
                $ttdPath = 'uploads/signatures/' . $fileName;
            }
        }

        // ==========================================================
        // 4. UPLOAD FILE PENDUKUNG (JIKA ADA)
        // ==========================================================
        $filePath = null;
        if ($request->hasFile('file_ppi')) {
            $filePath = $request->file('file_ppi')->store('ppi_uploads', 'public');
        }

        // ==========================================================
        // 5. SIMPAN KE DATABASE
        // ==========================================================
        Ppi::create([
            'no_ppi'       => $nomerOtomatis,
            'tanggal'      => Carbon::now(),
            'user_id'      => Auth::id(),
            'perangkat'    => $request->perangkat,
            'ba_kerusakan' => $request->ba_kerusakan, // atau 'deskripsi' sesuaikan dengan kolom DB
            'keterangan'   => $request->keterangan,
            'file_ppi'     => $filePath,
            'ttd_pemohon'  => $ttdPath, // Simpan path TTD
            'status'       => 'pending' // Masuk pending (menunggu Admin Cek)
        ]);

        return redirect()->route('pengguna.ppi.index')
            ->with('success', 'PPI Berhasil dibuat! No Tiket: ' . $nomerOtomatis);
    }

    public function index()
    {
        $riwayatPpi = Ppi::where('user_id', Auth::id())
                         ->latest()
                         ->get();

        return view('pengguna.ppi.index', [
            'title' => 'Riwayat Pengajuan PPI Saya',
            'menuPPI' => 'active',
            'riwayatPpi' => $riwayatPpi
        ]);
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil User Login
        $userId = Auth::id();
        
        // 2. Query Data dengan Filter
        $query = Ppi::where('user_id', $userId);
        
        $label = "Semua Data";
        
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', $request->tahun);
            
            $namaBulan = \Carbon\Carbon::createFromDate($request->tahun, $request->bulan, 1)->format('F');
            $label = "Bulan $namaBulan " . $request->tahun;
        } elseif ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
            $label = "Tahun " . $request->tahun;
        }

        $data = $query->latest()->get();

        // 3. Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pengguna.ppi.pdf_report', [
            'data' => $data,
            'label' => $label
        ]);
        
        // 4. Set ukuran kertas
        $pdf->setPaper('a4', 'portrait');

        // 5. Download / Stream
        return $pdf->stream('Laporan_PPI_' . time() . '.pdf');
    }
}