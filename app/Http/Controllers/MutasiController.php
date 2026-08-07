<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutasiAsset; 
use App\Models\BarangMasuk;
use App\Models\User;
use App\Models\LogSerahTerima;
use App\Notifications\MutasiNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutasiController extends Controller
{
    /**
     * Dashboard Monitoring & Eksekusi Mutasi Aset (Admin & Manager)
     */
    public function index()
    {
        $title = "Manajemen & Approval Mutasi Aset";

        // Filter daftar mutasi berdasarkan tahapan
        $pendingApprovals = MutasiAsset::with(['barangMasuk.masterBarang', 'userAsal', 'userTujuan', 'pemohon'])
            ->where('status', 'Menunggu Approval Manager')
            ->latest()
            ->get();

        $readyForIT = MutasiAsset::with(['barangMasuk.masterBarang', 'userAsal', 'userTujuan', 'pemohon', 'approver'])
            ->where('status', 'Disetujui Manager')
            ->latest()
            ->get();

        $completedMutations = MutasiAsset::with(['barangMasuk.masterBarang', 'userAsal', 'userTujuan', 'pemohon', 'approver', 'logSerahTerima'])
            ->whereIn('status', ['Menunggu TTD BAST', 'Selesai', 'Ditolak Manager'])
            ->latest()
            ->get();

        $riwayatMutasi = MutasiAsset::with(['barangMasuk.masterBarang', 'userAsal', 'userTujuan', 'pemohon', 'approver'])
            ->latest()
            ->get();

        $assets = BarangMasuk::with(['masterBarang', 'pemegang'])
            ->whereNotIn('status', ['Dimusnahkan', 'Hilang'])
            ->get();

        $users = User::all();

        return view('admin.mutasi.index', compact(
            'title', 
            'pendingApprovals', 
            'readyForIT', 
            'completedMutations', 
            'riwayatMutasi', 
            'assets', 
            'users'
        ));
    }

    /**
     * Input Mutasi Langsung dari Admin IT (atau Pengajuan Baru)
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_masuk_id' => 'required|exists:barang_masuk,id',
            'user_tujuan_id'  => 'required|exists:users,id',
            'keterangan'      => 'nullable|string',
        ]);

        $asset = BarangMasuk::findOrFail($request->barang_masuk_id);

        $mutasi = MutasiAsset::create([
            'barang_masuk_id' => $asset->id,
            'user_asal_id'    => $asset->user_pemegang_id,
            'user_tujuan_id'  => $request->user_tujuan_id,
            'pemohon_id'      => Auth::id(),
            'approved_by_id'  => Auth::id(),
            'status'          => 'Disetujui Manager',
            'keterangan'      => $request->keterangan ?? 'Pengajuan mutasi langsung oleh Admin IT',
            'tanggal_mutasi'  => now(),
        ]);

        // Langsung eksekusi mutasi fisik & terbitkan BAST
        return $this->processByAdmin($request, $mutasi->id);
    }

    /**
     * Manager / Atasan Menyutujui Pengajuan Mutasi
     */
    public function approveByManager(Request $request, $id)
    {
        $mutasi = MutasiAsset::with(['barangMasuk', 'pemohon'])->findOrFail($id);

        $mutasi->update([
            'status'         => 'Disetujui Manager',
            'approved_by_id' => Auth::id(),
        ]);

        // Notifikasi ke Pemohon
        if ($mutasi->pemohon) {
            $mutasi->pemohon->notify(new MutasiNotification(
                $mutasi,
                'Pengajuan Mutasi Disetujui Manager',
                'Pengajuan mutasi aset ' . ($mutasi->barangMasuk->kode_asset ?? '') . ' telah disetujui Manager dan akan diproses oleh Admin IT.',
                route('pengguna.mutasi.index')
            ));
        }

        // Notifikasi ke Admin IT
        $admins = User::whereIn('role', ['Admin', 'SuperAdmin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new MutasiNotification(
                $mutasi,
                'Mutasi Siap Dieksekusi IT',
                'Mutasi aset ' . ($mutasi->barangMasuk->kode_asset ?? '') . ' disetujui Manager. Silakan lakukan eksekusi fisik & terbitkan BAST.',
                route('mutasi.index')
            ));
        }

        return redirect()->back()->with('success', 'Pengajuan mutasi berhasil disetujui! Status diteruskan ke Admin IT.');
    }

    /**
     * Manager / Atasan Menolak Pengajuan Mutasi
     */
    public function rejectByManager(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi untuk menginfokan pemohon.'
        ]);

        $mutasi = MutasiAsset::with(['barangMasuk', 'pemohon'])->findOrFail($id);

        $mutasi->update([
            'status'           => 'Ditolak Manager',
            'alasan_penolakan' => $request->alasan_penolakan,
            'approved_by_id'   => Auth::id(),
        ]);

        // Kirim Notifikasi Penolakan ke Pemohon
        if ($mutasi->pemohon) {
            $mutasi->pemohon->notify(new MutasiNotification(
                $mutasi,
                'Pengajuan Mutasi Ditolak',
                'Pengajuan mutasi aset ' . ($mutasi->barangMasuk->kode_asset ?? '') . ' ditolak oleh Manager. Alasan: ' . $request->alasan_penolakan,
                route('pengguna.mutasi.index')
            ));
        }

        return redirect()->back()->with('success', 'Pengajuan mutasi telah ditolak. Notifikasi penolakan berhasil dikirim ke pemohon.');
    }

    /**
     * Admin IT Mengesahkan & Memproses Mutasi Fisik serta Menerbitkan BAST Digital
     */
    public function processByAdmin(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $mutasi = MutasiAsset::with(['barangMasuk', 'userTujuan', 'pemohon'])->findOrFail($id);
            $asset = $mutasi->barangMasuk;

            // 1. Perbarui Pemegang & Status pada BarangMasuk
            $asset->update([
                'user_pemegang_id' => $mutasi->user_tujuan_id,
                'status'           => 'Digunakan',
            ]);

            // 2. Terbitkan Log BAST Serah Terima untuk ditandatangani User Baru
            $bast = LogSerahTerima::create([
                'barang_masuk_id'      => $asset->id,
                'user_pemegang_id'     => $mutasi->user_tujuan_id,
                'admin_id'             => Auth::id(),
                'tanggal_serah_terima' => now(),
                'keterangan'           => 'Mutasi Aset: ' . ($mutasi->keterangan ?? 'Pemindahan Pemegang Aset'),
                'kondisi_saat_serah'   => 'Baik (Hasil Mutasi)',
                'status'               => 'menunggu_ttd_user',
            ]);

            // 3. Update status mutasi & hubungkan ke log_serah_terima_id
            $mutasi->update([
                'status'              => 'Menunggu TTD BAST',
                'log_serah_terima_id' => $bast->id,
                'tanggal_mutasi'      => now(),
            ]);

            DB::commit();

            // Notifikasi ke User Baru untuk TTD BAST
            if ($mutasi->userTujuan) {
                $mutasi->userTujuan->notify(new MutasiNotification(
                    $mutasi,
                    'Tanda Tangan BAST Mutasi Aset',
                    'Aset ' . ($asset->kode_asset ?? '') . ' telah dimutasi kepada Anda. Silakan lakukan tanda tangan digital BAST.',
                    route('pengguna.userbast.index')
                ));
            }

            return redirect()->route('mutasi.index')
                ->with('success', 'Fisik barang berhasil dimutasi! BAST digital telah diterbitkan untuk ditandatangani user baru.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses mutasi: ' . $e->getMessage());
        }
    }
}