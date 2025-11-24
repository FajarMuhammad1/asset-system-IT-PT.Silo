<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ppi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PpiController extends Controller
{
    /**
     * Tampilkan form buat bikin PPI.
     */
    public function create()
    {
        return view('pengguna.ppi.create', [
            'title' => 'Buat Pengajuan PPI',
            'menuPPI' => 'active'
        ]);
    }

    /**
     * Simpan PPI baru + Generate Nomor Otomatis (Format Romawi & Perusahaan).
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'perangkat'    => 'required|string|max:255',
            'ba_kerusakan' => 'required|string',
            'keterangan'   => 'nullable|string',
            'file_ppi'     => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        // 2. LOGIKA GENERATE NOMOR PPI (DINAMIS)
        
        $user = Auth::user();
        $now = Carbon::now();
        $tahun = $now->format('Y'); 
        $bulanAngka = $now->format('n'); // 1-12
        
        // A. Konversi Bulan ke Romawi
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $bulanRoman = $romanMonths[$bulanAngka];

        // B. Tentukan Kode Perusahaan dari User
        // Pastikan di tabel 'users' ada kolom 'perusahaan'
        $userPerusahaan = strtolower($user->perusahaan ?? '');
        $kodePerusahaan = 'CORP'; // Default jika tidak dikenali

        if ($userPerusahaan == 'bci') {
            $kodePerusahaan = 'BCI';
        } elseif ($userPerusahaan == 'silo') {
            $kodePerusahaan = 'SILO';
        } elseif ($userPerusahaan == 'sbk') {
            $kodePerusahaan = 'SBK';
        } 
        // Tambahkan else if lain jika ada perusahaan lain

        // C. Bikin Pola (Pattern) untuk Pencarian DB
        // Format: .PPI-[PERUSAHAAN].[ROMAWI].[TAHUN]
        // Contoh: .PPI-BCI.XI.2025
        $pattern = ".PPI-{$kodePerusahaan}.{$bulanRoman}.{$tahun}";

        // D. Cari Nomor Urut Terakhir
        // Kita cari data yang akhiran nomornya sama dengan $pattern
        $lastData = Ppi::where('no_ppi', 'like', "%{$pattern}")
                       ->latest('id')
                       ->first();
        
        $urut = 1; // Default mulai dari 1

        if ($lastData) {
            // Contoh data: "0005.PPI-BCI.XI.2025"
            // Kita pecah berdasarkan titik pertama
            $parts = explode('.', $lastData->no_ppi);
            $lastUrut = (int) $parts[0]; // Ambil angka "0005" jadi 5
            $urut = $lastUrut + 1;       // Jadi 6
        }
        
        // Format jadi 4 digit (0001, 0002, dst)
        $nomorUrut = str_pad($urut, 4, '0', STR_PAD_LEFT); 

        // E. Gabung Semua Jadi Nomor Final
        // Hasil: 0006.PPI-BCI.XI.2025
        $nomerOtomatis = $nomorUrut . $pattern; 


        // 3. Handle Upload File
        $filePath = null;
        if ($request->hasFile('file_ppi')) {
            $filePath = $request->file('file_ppi')->store('ppi_uploads', 'public');
        }

        // 4. Simpan ke Database
        Ppi::create([
            'no_ppi'       => $nomerOtomatis,
            'tanggal'      => Carbon::now(),
            'user_id'      => Auth::id(),
            'perangkat'    => $request->perangkat,
            'ba_kerusakan' => $request->ba_kerusakan,
            'keterangan'   => $request->keterangan,
            'file_ppi'     => $filePath,
            'status'       => 'pending'
        ]);

        return redirect()->route('pengguna.dashboard')
            ->with('success', 'PPI Berhasil dibuat! No Tiket: ' . $nomerOtomatis);
    }

    /**
     * Tampilkan Riwayat PPI User
     */
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
}