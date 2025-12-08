<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ppi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PpiController extends Controller
{
    public function create()
    {
        return view('pengguna.ppi.create', [
            'title' => 'Buat Pengajuan PPI',
            'menuPPI' => 'active'
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'perangkat'    => 'required|string|max:255',
            'ba_kerusakan' => 'required|string',
            'keterangan'   => 'nullable|string',
            'file_ppi'     => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Ambil user
        $user = Auth::user();

        // 2. Tanggal & Bulan ke Romawi
        $now = Carbon::now();
        $tahun = $now->format('Y');
        $bulanAngka = $now->format('n');

        $romanMonths = [
            1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',
            7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'
        ];
        $bulanRoman = $romanMonths[$bulanAngka];

        // 3. LOGIKA PERUSAHAAN OTOMATIS (PT atau tanpa PT)
        $raw = strtoupper(trim($user->perusahaan ?? ''));

        if (str_contains($raw, 'PT')) {

            // Pisahkan berdasarkan spasi dan titik
            $parts = preg_split('/[ .]+/', $raw);

            // Buang PT
            $cleanParts = array_filter($parts, fn($p) => $p !== 'PT');

            // Gabungkan kembali jadi nama perusahaan
            $kodePerusahaan = implode(' ', $cleanParts);

        } else {
            // Jika tidak ada PT, ambil apa adanya
            $kodePerusahaan = $raw;
        }

        // Jika masih kosong, default
        if (!$kodePerusahaan) {
            $kodePerusahaan = "CORP";
        }

        // 4. Pola generate nomor
        $pattern = ".PPI-{$kodePerusahaan}.{$bulanRoman}.{$tahun}";

        // 5. Ambil nomor terakhir berdasarkan pattern
        $lastData = Ppi::where('no_ppi', 'like', "%{$pattern}")
                       ->latest('id')
                       ->first();

        $urut = 1;

        if ($lastData) {
            $parts = explode('.', $lastData->no_ppi);
            $lastUrut = (int) $parts[0];
            $urut = $lastUrut + 1;
        }

        $nomorUrut = str_pad($urut, 4, '0', STR_PAD_LEFT);

        // 6. Gabungkan menjadi nomor final
        $nomerOtomatis = $nomorUrut . $pattern;

        // 7. Upload file jika ada
        $filePath = null;
        if ($request->hasFile('file_ppi')) {
            $filePath = $request->file('file_ppi')->store('ppi_uploads', 'public');
        }

        // 8. Simpan ke database
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
