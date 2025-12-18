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
use Barryvdh\DomPDF\Facade\Pdf; // [BARU] Import Library PDF

class BarangKeluarController extends Controller
{
    /**
     * FORM ADMIN UNTUK MEMBUAT BAST
     */
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
     * PROSES SIMPAN BAST
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_masuk_id'      => 'required|exists:barang_masuk,id',
            'user_pemegang_id'     => 'required|exists:users,id',
            'tanggal_serah_terima' => 'required|date',
            'keterangan'           => 'nullable|string',
            'foto_bukti'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ttd_penerima'         => 'nullable|string', 
            'ttd_petugas'          => 'nullable|string', 
        ]);

        DB::beginTransaction();
        try {
            $aset = BarangMasuk::findOrFail($request->barang_masuk_id);

            if ($aset->status !== 'Stok') {
                throw new \Exception("Aset ini sudah tidak tersedia (bukan Stok).");
            }

            $isDirectHandover = $request->filled('ttd_penerima') && $request->filled('ttd_petugas');
            
            $fotoPath = null;
            if ($request->hasFile('foto_bukti')) {
                $fotoPath = $request->file('foto_bukti')->store('bukti_serah_terima', 'public');
            }

            $log = LogSerahTerima::create([
                'barang_masuk_id'      => $aset->id,
                'user_pemegang_id'     => $request->user_pemegang_id,
                'admin_id'             => Auth::id(), 
                'tanggal_serah_terima' => $request->tanggal_serah_terima,
                'keterangan'           => $request->keterangan,
                'foto_bukti'           => $fotoPath,
                'kondisi_saat_serah'   => $aset->kondisi ?? 'Baik',
                'ttd_penerima'         => $request->ttd_penerima,
                'ttd_petugas'          => $request->ttd_petugas, 
                'status'               => $isDirectHandover ? 'selesai' : 'menunggu_ttd_user'
            ]);

            if ($isDirectHandover) {
                $aset->update([
                    'status' => 'Dipakai',
                    'user_pemegang_id' => $request->user_pemegang_id,
                    'lokasi_sekarang' => 'User ID: ' . $request->user_pemegang_id
                ]);
                
                if ($aset->surat_jalan_id) {
                    $this->checkAndCloseSuratJalan($aset->surat_jalan_id);
                }

                $pesan = 'BAST Berhasil! Aset resmi diserahkan & status Selesai.';
            } else {
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
     * [BARU] CETAK PDF BAST
     * Menghasilkan PDF profesional untuk dokumen serah terima
     */
    public function cetakBast($id)
    {
        // 1. Ambil data dengan relasi lengkap
        $log = LogSerahTerima::with([
            'aset.masterBarang',  // Untuk info nama barang & spek
            'aset.suratJalan',    // Untuk info No SJ/PO
            'pemegang',           // User Pihak Kedua
            'admin'               // Admin Pihak Pertama
        ])->findOrFail($id);

        // 2. Siapkan data untuk view
        $data = [
            'title' => 'BAST - ' . $log->aset->kode_asset,
            'log' => $log,
            'tanggal_cetak' => now()->translatedFormat('d F Y')
        ];

        // 3. Load View PDF dan set ukuran kertas
        $pdf = Pdf::loadView('admin.barangkeluar.pdf_bast', $data);
        $pdf->setPaper('a4', 'portrait');

        // 4. Stream (Preview di browser)
        return $pdf->stream('BAST-' . $log->aset->kode_asset . '.pdf');
    }

    /**
     * USER TANDA TANGAN
     */
    public function userSign(Request $request, $id)
    {
        $request->validate(['ttd_penerima' => 'required|string']);

        $log = LogSerahTerima::findOrFail($id);
        
        $log->update([
            'ttd_penerima' => $request->ttd_penerima, 
            'status' => 'menunggu_ttd_admin'
        ]);
        
        return back()->with('success', 'TTD User berhasil disimpan.');
    }

    /**
     * ADMIN TANDA TANGAN (FINALISASI)
     */
    public function adminSign(Request $request, $id)
    {
        $request->validate(['ttd_petugas' => 'required|string']);

        $log = LogSerahTerima::with('aset')->findOrFail($id);
        
        $log->update([
            'ttd_petugas' => $request->ttd_petugas, 
            'status' => 'selesai'
        ]);

        if ($log->aset) {
            $log->aset->update([
                'status' => 'Dipakai',
                'user_pemegang_id' => $log->user_pemegang_id,
                'lokasi_sekarang' => 'User ID: ' . $log->user_pemegang_id
            ]);

            if ($log->aset->surat_jalan_id) {
                $this->checkAndCloseSuratJalan($log->aset->surat_jalan_id);
            }
        }

        return back()->with('success', 'TTD Admin disimpan. BAST Selesai.');
    }

    /**
     * HELPER: Cek Close Surat Jalan
     */
    private function checkAndCloseSuratJalan($suratJalanId)
    {
        $suratJalan = SuratJalan::with('BarangMasuk')->find($suratJalanId);

        if (!$suratJalan) return;

        $totalItems = $suratJalan->barangMasuk->count();
        if ($totalItems === 0) return;

        $completedItems = $suratJalan->barangMasuk
            ->whereIn('status', ['Dipakai', 'Rusak']) 
            ->count();

        if ($completedItems === $totalItems) {
            $suratJalan->update(['is_bast_submitted' => true]);
        } else {
            $suratJalan->update(['is_bast_submitted' => false]);
        }
    }
}