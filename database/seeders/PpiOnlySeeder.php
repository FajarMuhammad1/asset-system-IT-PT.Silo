<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PpiOnlySeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // 1. BUAT / AMBIL AKUN PENGGUNA (STAFF)
        $pengguna = DB::table('users')->where('email', 'staff@perusahaan.com')->first();
        
        if (!$pengguna) {
            $penggunaId = DB::table('users')->insertGetId([
                'nama'       => 'Budi Karyawan (Pengguna)', // <--- Diganti menjadi 'nama'
                'email'      => 'staff@perusahaan.com',
                'password'   => bcrypt('password'),
                'role'       => 'Pengguna', // <--- Ditambahkan sesuai database
                'status'     => 'Aktif',    // <--- Ditambahkan sesuai database
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $penggunaId = $pengguna->id;
        }

        // 2. SIMULASI ALUR PENGAJUAN PPI
        $dataPpi = [
            // TAHAP 1: Baru diinput oleh Pengguna (Menunggu Admin)
            [
                'no_ppi'         => '0001.PPI-MKT.VI.2026',
                'tanggal'        => $now->format('Y-m-d'),
                'user_id'        => $penggunaId, 
                'perangkat'      => 'Kabel LAN 50 Meter',
                'ba_kerusakan'   => 'menambah',
                'keterangan'     => 'Kebutuhan jaringan untuk ruangan marketing baru',
                'tgl_approve'    => null,
                'status'         => 'pending',
                'alasan_tolak'   => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],

            // TAHAP 2: Sudah diperiksa Admin, diteruskan ke Super Admin
            [
                'no_ppi'         => '0002.PPI-HRD.VI.2026',
                'tanggal'        => $now->copy()->subDays(2)->format('Y-m-d'),
                'user_id'        => $penggunaId,
                'perangkat'      => 'Printer Epson L3210',
                'ba_kerusakan'   => 'penggantian',
                'keterangan'     => 'Printer HRD lama sudah rusak total (tinta bocor)',
                'tgl_approve'    => null,
                'status'         => 'pending_superadmin',
                'alasan_tolak'   => null,
                'created_at'     => $now->copy()->subDays(2),
                'updated_at'     => $now->copy()->subDays(1),
            ],

            // TAHAP 3: Sukses Di-approve oleh Super Admin
            [
                'no_ppi'         => '0003.PPI-IT.VI.2026',
                'tanggal'        => $now->copy()->subDays(10)->format('Y-m-d'),
                'user_id'        => $penggunaId,
                'perangkat'      => 'Laptop Lenovo Thinkpad',
                'ba_kerusakan'   => 'penambahan',
                'keterangan'     => 'Pengadaan unit untuk staf baru',
                'tgl_approve'    => $now->copy()->subDays(8)->format('Y-m-d H:i:s'),
                'status'         => 'disetujui',
                'alasan_tolak'   => null,
                'created_at'     => $now->copy()->subDays(10),
                'updated_at'     => $now->copy()->subDays(8),
            ],

            // TAHAP 4: Ditolak oleh Super Admin
            [
                'no_ppi'         => '0004.PPI-FIN.VI.2026',
                'tanggal'        => $now->copy()->subDays(15)->format('Y-m-d'),
                'user_id'        => $penggunaId,
                'perangkat'      => 'Mouse Wireless Logitech',
                'ba_kerusakan'   => 'penggantian',
                'keterangan'     => 'Mouse sebelumnya klik kirinya rusak',
                'tgl_approve'    => null,
                'status'         => 'ditolak',
                'alasan_tolak'   => 'Gunakan stok lama di gudang IT, masih ada 2 unit',
                'created_at'     => $now->copy()->subDays(15),
                'updated_at'     => $now->copy()->subDays(14),
            ],
        ];

        // 3. MASUKKAN DATA (Menggunakan Foreach)
        foreach ($dataPpi as $data) {
            DB::table('ppis')->insert($data);
        }
    }
}