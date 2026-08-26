<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MutasiAsset;
use App\Models\BarangMasuk;
use App\Models\User;
use App\Notifications\MutasiNotification;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PenggunaMutasiController extends Controller
{
    /**
     * Tampilkan riwayat pengajuan mutasi milik pengguna yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();
        $title = "Pengajuan Mutasi Aset";

        $riwayatPengajuan = MutasiAsset::with(['barangMasuk.masterBarang', 'userAsal', 'userTujuan', 'approver'])
            ->where('pemohon_id', $user->id)
            ->orWhere('user_tujuan_id', $user->id)
            ->latest()
            ->get();

        return view('pengguna.mutasi.index', compact('title', 'riwayatPengajuan'));
    }

    /**
     * Form pembuatan pengajuan mutasi aset oleh User Pemohon.
     */
    public function create()
    {
        $user = Auth::user();
        $title = "Form Pengajuan Mutasi Aset";

        // Ambil aset yang saat ini dipegang oleh user yang sedang login
        $myAssets = BarangMasuk::with('masterBarang')
            ->where('user_pemegang_id', $user->id)
            ->get();

        // Jika user tidak pegang aset, ambil semua aset aktif agar tetap bisa mengajukan jika diperlukan
        if ($myAssets->isEmpty()) {
            $myAssets = BarangMasuk::with('masterBarang')
                ->whereNotIn('status', ['Dimusnahkan', 'Hilang'])
                ->get();
        }

        // Ambil list karyawan lain untuk tujuan mutasi
        $users = User::where('id', '!=', $user->id)->get();

        return view('pengguna.mutasi.create', compact('title', 'myAssets', 'users'));
    }

    /**
     * Simpan pengajuan mutasi baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_masuk_id' => 'required|exists:barang_masuk,id',
            'user_tujuan_id'  => 'required|exists:users,id',
            'lokasi_baru'     => 'nullable|string|max:255',
            'keterangan'      => 'required|string|max:500',
        ], [
            'barang_masuk_id.required' => 'Pilih aset yang ingin dimutasi.',
            'user_tujuan_id.required'  => 'Pilih penerima baru (user tujuan).',
            'keterangan.required'      => 'Keterangan/alasan mutasi wajib diisi.',
        ]);

        $asset = BarangMasuk::findOrFail($request->barang_masuk_id);

        $mutasi = MutasiAsset::create([
            'barang_masuk_id' => $asset->id,
            'user_asal_id'    => $asset->user_pemegang_id,
            'user_tujuan_id'  => $request->user_tujuan_id,
            'pemohon_id'      => Auth::id(),
            'lokasi_baru'     => $request->lokasi_baru,
            'keterangan'      => $request->keterangan,
            'status'          => 'Menunggu Approval Manager',
            'tanggal_mutasi'  => now(),
        ]);

        // Kirim notifikasi ke SuperAdmin untuk direview
        $superAdmins = User::where('role', 'SuperAdmin')->get();

        foreach ($superAdmins as $superAdmin) {
            $superAdmin->notify(new MutasiNotification(
                $mutasi,
                'Pengajuan Mutasi Aset Baru',
                Auth::user()->nama . ' mengajukan mutasi untuk aset ' . ($asset->kode_asset ?? 'Aset') . '.',
                route('mutasi.index')
            ));
        }

        return redirect()->route('pengguna.mutasi.index')
            ->with('success', 'Pengajuan mutasi berhasil dikirim dan menunggu review Super Admin.');
    }

    /**
     * Cetak Dokumen PDF Mutasi Aset untuk Pengguna
     */
    public function cetakPdf($id)
    {
        $user = Auth::user();
        $mutasi = MutasiAsset::with([
            'barangMasuk.masterBarang',
            'userAsal',
            'userTujuan',
            'pemohon',
            'approver',
            'logSerahTerima.admin'
        ])->where(function($query) use ($user) {
            $query->where('pemohon_id', $user->id)
                  ->orWhere('user_tujuan_id', $user->id)
                  ->orWhere('user_asal_id', $user->id);
        })->findOrFail($id);

        $path = public_path('image/images.png'); 
        $logoBase64 = null;
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImg = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
        }

        $kodeAsset = $mutasi->barangMasuk->kode_asset ?? ('MUTASI-' . $mutasi->id);
        $cleanKode = preg_replace('/[^A-Za-z0-9_\-]/', '_', $kodeAsset);
        $namaFile = 'Berita_Acara_Mutasi_' . $cleanKode . '_' . date('Ymd_His');

        $data = [
            'title'         => 'Berita Acara Mutasi Aset - ' . $kodeAsset,
            'mutasi'        => $mutasi,
            'logo'          => $logoBase64,
            'tanggal_cetak' => Carbon::parse($mutasi->tanggal_mutasi ?? $mutasi->created_at)->translatedFormat('d F Y'),
            'hari_ini'      => Carbon::now()->translatedFormat('d F Y')
        ];

        $pdf = Pdf::loadView('admin.mutasi.pdf_mutasi', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($namaFile . '.pdf');
    }
}
