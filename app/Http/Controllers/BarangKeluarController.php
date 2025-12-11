<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\User;
use App\Models\LogSerahTerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BarangKeluarController extends Controller
{
    /**
     * FORM ADMIN UNTUK MEMBUAT BAST
     */
    public function create()
    {
        // Ambil barang yang statusnya 'Stok'
        $asetStok = BarangMasuk::with('masterBarang', 'suratJalan')
            ->where('status', 'Stok')
            ->get();

        // Ambil user karyawan (bukan admin)
        $users = User::where('role', '!=', 'admin')->orderBy('nama')->get();

        return view('admin.barangkeluar.create', [
            'title' => 'Form Serah Terima Aset (BAST)',
            'asetStok' => $asetStok,
            'users' => $users,
        ]);
    }

    /**
     * API / AJAX DETAILS ASET
     */
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
                'kategori'      => $aset->masterBarang->kategori->nama_kategori ?? '-', 
                'merk'          => $aset->masterBarang->merk ?? '-',
                'model'         => $aset->masterBarang->nama_barang ?? '-',
                'spesifikasi'   => $aset->masterBarang->spesifikasi ?? '-',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Aset tidak ditemukan: ' . $e->getMessage()], 404);
        }
    }

    /**
     * PROSES SIMPAN BAST (Hybrid: Draft atau Langsung Selesai)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'barang_masuk_id'      => 'required|exists:barang_masuk,id',
            'user_pemegang_id'     => 'required|exists:users,id',
            'tanggal_serah_terima' => 'required|date',
            'keterangan'           => 'nullable|string',
            'foto_bukti'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ttd_penerima'         => 'nullable|string', // Base64
            'ttd_petugas'          => 'nullable|string', // Base64
        ]);

        DB::beginTransaction();
        try {
            $aset = BarangMasuk::findOrFail($request->barang_masuk_id);

            // Validasi Status Barang
            if ($aset->status !== 'Stok') {
                throw new \Exception("Aset ini sudah tidak tersedia (bukan Stok).");
            }

            // CEK: Apakah Tanda Tangan diisi di form?
            $isDirectHandover = $request->filled('ttd_penerima') && $request->filled('ttd_petugas');
            
            // Upload Foto jika ada
            $fotoPath = null;
            if ($request->hasFile('foto_bukti')) {
                $fotoPath = $request->file('foto_bukti')->store('bukti_serah_terima', 'public');
            }

            // Insert ke log_serah_terima
            LogSerahTerima::create([
                'barang_masuk_id'      => $aset->id,
                'user_pemegang_id'     => $request->user_pemegang_id,
                
                // Pastikan 'admin_id' terisi user yang sedang login
                'admin_id'             => Auth::id(), 
                
                'tanggal_serah_terima' => $request->tanggal_serah_terima,
                'keterangan'           => $request->keterangan,
                'foto_bukti'           => $fotoPath,
                'kondisi_saat_serah'   => $aset->kondisi ?? 'Baik',
                
                // Simpan TTD jika ada
                'ttd_penerima'         => $request->ttd_penerima,
                'ttd_petugas'          => $request->ttd_petugas, 
                
                // Logic Status
                'status'               => $isDirectHandover ? 'selesai' : 'menunggu_ttd_user'
            ]);

            // UPDATE STATUS ASET: Hanya jika langsung selesai (TTD lengkap)
            if ($isDirectHandover) {
                $aset->update([
                    'status' => 'Dipakai',
                    'user_pemegang_id' => $request->user_pemegang_id,
                    'lokasi_sekarang' => 'User ID: ' . $request->user_pemegang_id
                ]);
                
                $pesan = 'BAST Berhasil! Aset resmi diserahkan & status Selesai.';
            } else {
                // Jika cuma Draft, status aset tetap 'Stok' dulu
                $pesan = 'Draft BAST dibuat. Menunggu User login untuk tanda tangan.';
            }

            DB::commit();

            return redirect()->route('barangkeluar.index')->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * DAFTAR BAST
     */
    public function index()
    {
        $logs = LogSerahTerima::with(['aset.masterBarang', 'pemegang', 'admin'])
            ->latest()
            ->get();

        return view('admin.barangkeluar.index', [
            'title' => 'Riwayat Serah Terima (BAST)',
            'logs'  => $logs
        ]);
    }

    /**
     * DETAIL 1 BAST
     */
    public function show($id)
    {
        $log = LogSerahTerima::with(['aset.masterBarang', 'pemegang', 'admin'])
            ->findOrFail($id);

        return view('admin.barangkeluar.show', [
            'title' => 'Detail BAST',
            'log' => $log
        ]);
    }

    /**
     * USER TANDA TANGAN (Dipanggil dari halaman User)
     */
    public function userSign(Request $request, $id)
    {
        // Validasi input 'ttd_penerima' (Sesuai name di form user)
        $request->validate(['ttd_penerima' => 'required|string']);

        $log = LogSerahTerima::findOrFail($id);
        
        $log->update([
            'ttd_penerima' => $request->ttd_penerima, // Perbaikan nama field
            'status' => 'menunggu_ttd_admin'
        ]);
        
        return back()->with('success', 'TTD User berhasil disimpan.');
    }

    /**
     * ADMIN TANDA TANGAN (Dipanggil dari halaman Detail Admin)
     */
    public function adminSign(Request $request, $id)
    {
        // Validasi input 'ttd_petugas' (Sesuai name di form admin)
        $request->validate(['ttd_petugas' => 'required|string']);

        $log = LogSerahTerima::findOrFail($id);
        
        $log->update([
            'ttd_petugas' => $request->ttd_petugas, // Perbaikan nama field
            'status' => 'selesai'
        ]);

        // Finalisasi aset menjadi 'Dipakai'
        $log->aset->update([
            'status' => 'Dipakai',
            'user_pemegang_id' => $log->user_pemegang_id,
            'lokasi_sekarang' => 'User ID: ' . $log->user_pemegang_id
        ]);

        return back()->with('success', 'TTD Admin disimpan. BAST Selesai.');
    }
}