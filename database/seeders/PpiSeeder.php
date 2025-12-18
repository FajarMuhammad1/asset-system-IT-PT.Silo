<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class PpiSeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil ID semua user dengan role 'Pengguna'
        $users = DB::table('users')->where('role', 'Pengguna')->pluck('id')->toArray();

        if (empty($users)) {
            $this->command->info('Tidak ada user dengan role Pengguna. Jalankan UserSeeder dulu!');
            return;
        }

        // 2. Tentukan "Si Paling Sering Rusak" (User pertama di array)
        // User ini akan mendominasi data (misal: Budi Santoso)
        $heavyUser = $users[0]; 

        $faker = Faker::create('id_ID');
        
        // Kosongkan tabel PPI dulu (Opsional, agar bersih)
        DB::table('ppis')->truncate();

        $data = [];
        $totalData = 70; // Kita buat 70 data setahun ini

        // List opsi random biar variatif
        $perangkatList = [
            'Laptop Dell Latitude', 'Printer Epson L3110', 'PC Desktop Lenovo', 
            'Monitor Samsung 24"', 'Keyboard Logitech', 'Mouse Wireless', 
            'Scanner Canon', 'Jaringan WiFi', 'Kabel LAN', 'UPS Prolink'
        ];

        $masalahList = [
            'Layar blue screen tiba-tiba', 'Hasil print putus-putus', 'Tidak bisa connect internet',
            'Minta install Microsoft Visio', 'PC sangat lambat saat booting', 'Tombol keyboard macet',
            'Baterai drop cepat habis', 'Lampu indikator kedap-kedip', 'Kabel digigit tikus',
            'Suara kipas PC berisik'
        ];

        $statusList = ['selesai', 'selesai', 'selesai', 'disetujui', 'pending', 'ditolak']; // Lebih banyak selesai biar rapi

        for ($i = 1; $i <= $totalData; $i++) {
            
            // LOGIKA: 60% Kemungkinan adalah Heavy User, 40% user lain
            // Ini menjawab request "siapa yang paling banyak input"
            $userId = ($faker->boolean(60)) ? $heavyUser : $faker->randomElement($users);

            // LOGIKA TANGGAL: Random dari Jan - Des 2025
            $bulan = $faker->numberBetween(1, 12);
            $hari  = $faker->numberBetween(1, 28); // Biar aman untuk Februari
            
            // Format Tanggal
            $date = Carbon::create(2025, $bulan, $hari, $faker->numberBetween(8, 16), $faker->numberBetween(0, 59));
            
            // Nomor PPI Dummy (Urut per bulan biar keren, tapi di sini random aja gpp)
            $blnRomawi = $this->getRomawi($bulan);
            $noPpi = "PPI/2025/{$blnRomawi}/" . str_pad($i, 3, '0', STR_PAD_LEFT);

            $data[] = [
                'user_id'      => $userId,
                'no_ppi'       => $noPpi,
                'perangkat'    => $faker->randomElement($perangkatList),
                'keterangan'   => $faker->randomElement($masalahList),
                'ba_kerusakan' => $faker->randomElement(['Perbaikan', 'Perbaikan', 'Penambahan']),
                'tanggal'      => $date->format('Y-m-d'),
                'status'       => $faker->randomElement($statusList),
                'created_at'   => $date->format('Y-m-d H:i:s'),
                'updated_at'   => $date->addHours(rand(1, 48))->format('Y-m-d H:i:s'), // Selesai 1-2 hari setelahnya
            ];
        }

        // Insert Batch
        DB::table('ppis')->insert($data);
    }

    // Helper function untuk bulan romawi
    private function getRomawi($bulan) {
        $map = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        return $map[$bulan];
    }
}