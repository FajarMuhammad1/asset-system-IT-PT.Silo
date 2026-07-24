<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BarangMasuk;
use App\Models\NilaiAset;
use Illuminate\Support\Facades\Schema;

class AssetReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel NilaiAset terlebih dahulu agar tidak duplikat
        Schema::disableForeignKeyConstraints();
        NilaiAset::truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Ambil SEMUA data Barang Masuk yang SEKARANG sudah ada di database Anda
        $daftarBarangSekarang = BarangMasuk::all();

        // Antisipasi jika ternyata tabel barang masuk Anda masih kosong
        if ($daftarBarangSekarang->isEmpty()) {
            $this->command->warn('⚠️ Tabel barang_masuks Anda saat ini kosong. Harap isi beberapa data barang dulu di aplikasi, baru jalankan seeder ini kembali.');
            return;
        }

        // 3. Loop setiap barang yang ada sekarang, lalu buatkan Nilai Aset dummy-nya
        foreach ($daftarBarangSekarang as $barang) {
            
            // Membuat nominal Nilai Awal acak secara logis (antara 2 Juta - 25 Juta)
            $nilaiAwal = rand(2, 25) * 1000000; 

            // Menghitung Nilai Sekarang secara otomatis berdasarkan "Status" barang saat ini
            if (strtolower($barang->status) === 'baik') {
                $nilaiSekarang = $nilaiAwal * 0.85; // Menyusut 15% jika kondisinya Baik
            } elseif (strtolower($barang->status) === 'rusak ringan') {
                $nilaiSekarang = $nilaiAwal * 0.50; // Menyusut 50% jika Rusak Ringan
            } else {
                $nilaiSekarang = $nilaiAwal * 0.15; // Menyusut 85% jika Rusak Berat/Lainnya
            }

            // Simpan ke tabel penilaian aset
            NilaiAset::create([
                'barang_masuk_id'   => $barang->id, // Menghubungkan ke ID barang Anda yang sekarang
                'tanggal_penilaian' => now()->subMonths(rand(1, 5))->format('Y-m-d'), // Tanggal acak (1-5 bulan lalu)
                'nilai_awal'        => $nilaiAwal,
                'nilai_sekarang'    => $nilaiSekarang,
            ]);
        }

        $count = $daftarBarangSekarang->count();
        $this->command->info("✅ Berhasil membuat {$count} data dummy Nilai Aset berdasarkan data barang Anda yang sekarang!");
    }
}