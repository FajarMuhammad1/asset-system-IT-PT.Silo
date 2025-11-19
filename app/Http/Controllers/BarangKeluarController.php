<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\User;
use App\Models\LogSerahTerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // <-- PENTING BUAT FILE

class BarangKeluarController extends Controller
{
    /**
     * Tampilkan Form BAST (Serah Terima)
     */
    public function create()
    {
        // 1. Ambil SEMUA ASET yang statusnya 'Stok'
        $asetStok = BarangMasuk::with('masterBarang', 'suratJalan')
                                ->where('status', 'Stok')
                                ->get();
                                    
        // 2. Ambil SEMUA USER (buat milih pemegang)
        // --- INI YANG DIPERBAIKI ---
        $users = User::orderBy('nama')->get(); // 'nama' diubah jadi 'name'

        return view('admin.barangkeluar.create', [
            'title' => 'Form  (Barang Keluar)',
            'asetStok' => $asetStok,
            'users' => $users,
        ]);
    }

    /**
     * FUNGSI BARU: Ambil detail Aset via AJAX
     */
    public function getAssetDetails(Request $request)
    {
        if (!$request->has('id')) {
            return response()->json(['error' => 'ID Aset tidak ada.'], 400);
        }

        try {
            $aset = BarangMasuk::with('masterBarang', 'suratJalan')
                                ->findOrFail($request->id);

            if ($aset->status != 'Stok') {
                return response()->json(['error' => 'Aset ini tidak berstatus Stok!'], 422);
            }
            
            return response()->json([
                'kode_asset' => $aset->kode_asset,
                'serial_number' => $aset->serial_number,
                'no_sj' => $aset->suratJalan->no_sj ?? 'N/A',
                'no_ppi' => $aset->suratJalan->no_ppi ?? 'N/A',
                'no_po' => $aset->suratJalan->no_po ?? 'N/A',
                'kategori' => $aset->masterBarang->kategori ?? 'N/A',
                'merk' => $aset->masterBarang->merk ?? 'N/A',
                'model' => $aset->masterBarang->nama_barang ?? 'N/A',
                'spesifikasi' => $aset->masterBarang->spesifikasi ?? 'N/A',
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Aset tidak ditemukan.'], 404);
        }
    }


    /**
     * Simpan data  (Serah Terima)
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'barang_masuk_id' => 'required|exists:barang_masuk,id',
            'user_pemegang_id' => 'required|exists:users,id',
            'tanggal_serah_terima' => 'required|date',
            'keterangan' => 'nullable|string',
            'ttd_penerima' => 'required|string', 
            'ttd_petugas' => 'required|string',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:4096',
        ]);

        DB::beginTransaction();
        try {
            $aset = BarangMasuk::find($request->barang_masuk_id);

            if ($aset->status != 'Stok') {
                throw new \Exception('Aset ini sudah tidak berstatus Stok.');
            }

            $logData = [
                'barang_masuk_id' => $aset->id,
                'user_pemegang_id' => $request->user_pemegang_id,
                'admin_id' => Auth::id(),
                'tanggal_serah_terima' => $request->tanggal_serah_terima,
                'keterangan' => $request->keterangan,
                'ttd_penerima' => $request->ttd_penerima,
                'ttd_petugas' => $request->ttd_petugas,
            ];

            if ($request->hasFile('foto_bukti')) {
                $path = $request->file('foto_bukti')->store('bukti_serah_terima', 'public');
                $logData['foto_bukti'] = $path;
            }

            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('file_serah_terima', 'public');
                $logData['file'] = $path;
            }

            // UPDATE ASETNYA
            $aset->status = 'Dipakai';
            $aset->user_pemegang_id = $request->user_pemegang_id;
            $aset->save();

            // CATAT DI LOG
            LogSerahTerima::create($logData);

            DB::commit();

            return redirect()->route('barangmasuk.index') 
                             ->with('success', 'Aset berhasil diserahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($logData['foto_bukti']) && Storage::disk('public')->exists($logData['foto_bukti'])) {
                Storage::disk('public')->delete($logData['foto_bukti']);
            }
            if (isset($logData['file']) && Storage::disk('public')->exists($logData['file'])) {
                Storage::disk('public')->delete($logData['file']);
            }
            
            return back()->withInput()->with('error', 'Gagal menyerahkan aset! Error: ' . $e->getMessage());
        }
    }


    // --- INI FUNGSI BARU YANG DITAMBAHKAN ---

    /**
     * Tampilkan list/riwayat semua  (Serah Terima)
     */
    public function index()
    {
        // Ambil semua log, urut dari terbaru
        // Kita pake 'with' relasi yang tadi kita bikin di Model
        $logs = LogSerahTerima::with([
            'aset.masterBarang', // Ambil info aset & masternya (Nama Barang)
            'pemegang',          // Ambil info user pemegang
            'admin'              // Ambil info admin yg nyerahin
        ])->latest()->get();

        return view('admin.barangkeluar.index', [
            'title' => 'Riwayat Serah Terima Aset ',
            'logs' => $logs
        ]);
    }

    /**
     * Tampilkan detail 1 BAST (termasuk TTD & Foto)
     */
    public function show($id)
    {
        $log = LogSerahTerima::with('aset.masterBarang', 'pemegang', 'admin')
                             ->findOrFail($id);

        return view('admin.barangkeluar.show', [
            'title' => 'Detail',
            'log' => $log
        ]);
    }

    // --- BATAS FUNGSI BARU ---
}