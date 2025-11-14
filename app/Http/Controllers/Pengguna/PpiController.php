<?php

namespace App\Http\Controllers\Pengguna; // <-- Pastiin namespacenya bener

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ppi; // <-- Panggil Model Ppi
use App\Models\User; // <-- Panggil Model User (buat relasi)
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
            'menuPPI' => 'active' // Biar sidebar nyala
        ]);
    }

    /**
     * Simpan PPI baru + Generate Nomor Otomatis.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Tetap sama)
        $request->validate([
            'perangkat'    => 'required|string|max:255',
            'ba_kerusakan' => 'required|string',
            'keterangan'   => 'nullable|string',
            'file_ppi'     => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        // 2. LOGIKA GENERATE NOMOR (VERSI BARU: ROMAWI + KODE DINAMIS)
        
        $user = Auth::user();
        $now = Carbon::now();
        $tahun = $now->format('Y'); // Hasil: "2025"
        
        // --- BAGIAN A: Dapetin Bulan Romawi ---
        $bulanAngka = $now->format('n'); // Angka bulan (Misal: 11)
        
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        
        $bulanRoman = $romanMonths[$bulanAngka]; // Hasil (kalo November): "XI"
        
        // --- BAGIAN B: Dapetin Kode Perusahaan (Dinamis) ---
        // Asumsi lo punya kolom 'perusahaan' di tabel 'users'
        // Ganti 'bci' / 'silo' / 'sbk' sesuai data di DB lo
        
        $kodePerusahaan = 'UNKNOWN'; // Default
        
        // Kita pake strtolower biar aman (BCI, bci, Bci semua jadi bci)
        switch (strtolower($user->perusahaan)) { 
            case 'bci':
                $kodePerusahaan = 'BCI';
                break;
            case 'silo':
                $kodePerusahaan = 'SILO';
                break;
            case 'sbk':
                $kodePerusahaan = 'SBK';
                break;
            // Tambahin perusahaan lain kalo perlu...
            default:
                $kodePerusahaan = 'CORP'; // Kalo gak ketemu, pake 'CORP'
        }


        // --- BAGIAN C: Bikin Pola (Pattern) ---
        // Format: .PPI-COMPANY.MONTH.YEAR
        $pattern = ".PPI-{$kodePerusahaan}.{$bulanRoman}.{$tahun}";
        // Hasil (Kalo user 'BCI' & bulan November): ".PPI-BCI.XI.2025"
        

        // --- BAGIAN D: Tentukan Nomor Urut (Reset per Bulan & per Kode) ---
        
        // Cari data terakhir yang polanya SAMA (buat nge-reset nomor urut)
        $lastData = Ppi::where('no_ppi', 'like', "%{$pattern}")
                       ->whereYear('created_at', $tahun)
                       ->whereMonth('created_at', $bulanAngka)
                       ->latest('id')
                       ->first();
        
        $urut = 1; // Nomor default kalo ini data pertama (untuk kode & bulan ini)

        if ($lastData) {
            // Kalo data ketemu (misal: "0003.PPI-BCI.XI.2025")
            $parts = explode('.', $lastData->no_ppi);
            $lastUrut = (int)$parts[0]; // Ambil angka 0003
            $urut = $lastUrut + 1; // Jadi 4
        }
        
        // Format 4 digit (0001, 0002, ..., 0004)
        $nomorUrut = str_pad($urut, 4, '0', STR_PAD_LEFT); 

        // Gabungin semua
        $nomerOtomatis = $nomorUrut . $pattern; 
        // Hasil: "0001.PPI-BCI.XI.2025"

        // 3. Handle Upload File (Tetap sama)
        $filePath = null;
        if ($request->hasFile('file_ppi')) {
            $filePath = $request->file('file_ppi')->store('ppi_uploads', 'public');
        }

        // 4. SIMPAN KE DATABASE (Tetap sama)
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

    // (Nanti tambahin fungsi index, show, edit buat user kalo perlu)
}