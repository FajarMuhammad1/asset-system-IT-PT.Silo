<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BarangMasukAlurSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // 1. AMBIL SEMUA DATA DARI SURAT JALAN DETAILS
        // Kita join dengan tabel surat_jalan agar tahu tanggal input dan nomor surat jalannya
        $sjDetails = DB::table('surat_jalan_details')
            ->join('surat_jalan', 'surat_jalan_details.surat_jalan_id', '=', 'surat_jalan.id_sj')
            ->select(
                'surat_jalan_details.surat_jalan_id',
                'surat_jalan_details.master_barang_id',
                'surat_jalan_details.qty',
                'surat_jalan.tanggal_input',
                'surat_jalan.no_sj'
            )
            ->get();

        if ($sjDetails->isEmpty()) {
            $this->command->info('Belum ada data di Surat Jalan. Silakan jalankan Seeder Surat Jalan terlebih dahulu.');
            return;
        }

        $jumlahMasuk = 0;

        // 2. LOOPING SETIAP BARANG DARI SURAT JALAN
        foreach ($sjDetails as $detail) {
            
            // Pengecekan cerdas: Apakah barang dari surat jalan ini sudah masuk ke gudang?
            $existingCount = DB::table('barang_masuk')
                ->where('surat_jalan_id', $detail->surat_jalan_id)
                ->where('master_barang_id', $detail->master_barang_id)
                ->count();

            // Jika jumlah barang yang sudah ada di gudang masih kurang dari qty di Surat Jalan
            if ($existingCount < $detail->qty) {
                
                $sisaQty = $detail->qty - $existingCount;

                // Looping sebanyak sisa kuantitas (qty) barang
                for ($i = 0; $i < $sisaQty; $i++) {
                    
                    // Generate Serial Number dan Kode Asset secara acak & unik
                    $randomStr = strtoupper(Str::random(4));
                    $serialNumber = 'SN-' . date('Ymd') . '-' . $randomStr . rand(10, 99);
                    $kodeAsset = 'AST/IT/' . date('Y') . '/' . rand(1000, 9999) . $randomStr;

                    // Tanggal barang masuk disamakan dengan tanggal input surat jalan
                    $tanggalMasuk = $detail->tanggal_input ? $detail->tanggal_input : $now->format('Y-m-d');

                    DB::table('barang_masuk')->insert([
                        'surat_jalan_id'   => $detail->surat_jalan_id,
                        'master_barang_id' => $detail->master_barang_id,
                        'serial_number'    => $serialNumber, // Wajib Unik
                        'kode_asset'       => $kodeAsset,    // Wajib Unik
                        'status'           => 'Stok',        // Status awal selalu Stok di gudang
                        'user_pemegang_id' => null,          // Null karena belum didistribusikan ke user
                        'tanggal_masuk'    => $tanggalMasuk,
                        'keterangan'       => 'Barang masuk dari Surat Jalan: ' . $detail->no_sj,
                        'created_at'       => Carbon::parse($tanggalMasuk),
                        'updated_at'       => Carbon::parse($tanggalMasuk),
                    ]);

                    $jumlahMasuk++;
                }
            }
        }

        // Output Pesan Hasil
        if ($jumlahMasuk > 0) {
            $this->command->info("Berhasil memasukkan {$jumlahMasuk} unit barang ke dalam Stok (Barang Masuk)!");
        } else {
            $this->command->info('Semua barang dari Surat Jalan sudah masuk ke data Barang Masuk. Tidak ada data baru.');
        }
    }
}