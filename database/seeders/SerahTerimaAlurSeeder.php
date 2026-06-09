<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SerahTerimaAlurSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // 1. Cari Admin IT yang bertugas menyerahkan barang
        $admin = DB::table('users')->whereIn('role', ['Admin', 'SuperAdmin'])->first();
        if (!$admin) {
            $admin = DB::table('users')->first(); // Fallback
        }

        // 2. Ambil semua barang di Gudang yang statusnya masih 'Stok'
        $barangStok = DB::table('barang_masuk')
            ->where('status', 'Stok')
            ->whereNull('user_pemegang_id')
            ->get();

        if ($barangStok->isEmpty()) {
            $this->command->info('Tidak ada barang berstatus "Stok" di Gudang. Silakan jalankan seeder Barang Masuk dulu.');
            return;
        }

        $jumlahSerahTerima = 0;

        foreach ($barangStok as $barang) {
            
            // 3. Lacak siapa User yang berhak menerima barang ini?
            $suratJalan = DB::table('surat_jalan')->where('id_sj', $barang->surat_jalan_id)->first();
            
            if ($suratJalan) {
                $ppi = DB::table('ppis')->where('no_ppi', $suratJalan->no_ppi)->first();
                
                if ($ppi && $ppi->user_id) {
                    
                    // --- PERBAIKAN ERROR FOREIGN KEY ---
                    // Cek apakah user_id dari tabel PPI benar-benar ada di tabel users
                    $cekUser = DB::table('users')->where('id', $ppi->user_id)->first();
                    
                    // Jika user pemohon tidak ditemukan (mungkin sudah dihapus), 
                    // gunakan ID admin/user pertama sebagai fallback agar tidak error
                    $validUserId = $cekUser ? $cekUser->id : $admin->id;
                    // ------------------------------------

                    // Waktu serah terima
                    $tanggalSerahTerima = Carbon::parse($barang->tanggal_masuk)->addDay();

                    // 4. INSERT KE TABEL LOG SERAH TERIMA
                    DB::table('log_serah_terima')->insert([
                        'status'               => 'selesai', 
                        'id_surat_jalan'       => $suratJalan->id_sj,
                        'barang_masuk_id'      => $barang->id,
                        'user_pemegang_id'     => $validUserId, // Menggunakan ID yang sudah divalidasi
                        'admin_id'             => $admin->id,    
                        'created_by'           => $admin->id,
                        'tanggal_serah_terima' => $tanggalSerahTerima->format('Y-m-d'),
                        'keterangan'           => 'Serah terima pengadaan perangkat ' . $ppi->perangkat,
                        'kondisi_saat_serah'   => 'Baik', 
                        'created_at'           => $tanggalSerahTerima,
                        'updated_at'           => $tanggalSerahTerima,
                    ]);

                    // 5. UPDATE TABEL BARANG MASUK
                    DB::table('barang_masuk')->where('id', $barang->id)->update([
                        'status'           => 'Digunakan',
                        'user_pemegang_id' => $validUserId, // Menggunakan ID yang sudah divalidasi
                        'updated_at'       => $tanggalSerahTerima,
                    ]);

                    $jumlahSerahTerima++;
                }
            }
        }

        // Output log
        if ($jumlahSerahTerima > 0) {
            $this->command->info("🎉 Berhasil melakukan serah terima untuk {$jumlahSerahTerima} unit barang!");
            $this->command->info("Status aset kini telah berubah dari 'Stok' menjadi 'Digunakan'.");
        } else {
            $this->command->info('Tidak ada data yang diproses. Cek relasi Surat Jalan dan PPI.');
        }
    }
}