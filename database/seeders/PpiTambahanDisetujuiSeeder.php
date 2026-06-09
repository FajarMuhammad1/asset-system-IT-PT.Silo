<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PpiTambahanDisetujuiSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // Ambil akun pengguna (Budi) yang sudah kita buat di seeder sebelumnya
        $pengguna = DB::table('users')->where('email', 'staff@perusahaan.com')->first();
        $penggunaId = $pengguna ? $pengguna->id : 1; 

        // 3 Data Dummy Baru (Semuanya sudah di-Approve Super Admin)
        $dataPpi = [
            [
                'no_ppi'         => '0005.PPI-IT.VI.2026',
                'tanggal'        => $now->copy()->subDays(12)->format('Y-m-d'),
                'user_id'        => $penggunaId,
                'perangkat'      => 'Monitor Dell 24 Inch',
                'ba_kerusakan'   => 'penambahan',
                'keterangan'     => 'Kebutuhan dual monitor untuk tim developer aplikasi',
                'tgl_approve'    => $now->copy()->subDays(10)->format('Y-m-d H:i:s'),
                'status'         => 'disetujui',
                'alasan_tolak'   => null,
                'created_at'     => $now->copy()->subDays(12),
                'updated_at'     => $now->copy()->subDays(10),
            ],
            [
                'no_ppi'         => '0006.PPI-NET.VI.2026',
                'tanggal'        => $now->copy()->subDays(20)->format('Y-m-d'),
                'user_id'        => $penggunaId,
                'perangkat'      => 'Switch Hub Cisco 24 Port',
                'ba_kerusakan'   => 'penggantian',
                'keterangan'     => 'Switch lama di lantai 2 sering RTO (mati mendadak karena panas)',
                'tgl_approve'    => $now->copy()->subDays(18)->format('Y-m-d H:i:s'),
                'status'         => 'disetujui',
                'alasan_tolak'   => null,
                'created_at'     => $now->copy()->subDays(20),
                'updated_at'     => $now->copy()->subDays(18),
            ],
            [
                'no_ppi'         => '0007.PPI-GA.VI.2026',
                'tanggal'        => $now->copy()->subDays(8)->format('Y-m-d'),
                'user_id'        => $penggunaId,
                'perangkat'      => 'Proyektor Epson XGA',
                'ba_kerusakan'   => 'penambahan',
                'keterangan'     => 'Fasilitas tambahan untuk ruang meeting VIP baru di lantai 3',
                'tgl_approve'    => $now->copy()->subDays(6)->format('Y-m-d H:i:s'),
                'status'         => 'disetujui',
                'alasan_tolak'   => null,
                'created_at'     => $now->copy()->subDays(8),
                'updated_at'     => $now->copy()->subDays(6),
            ]
        ];

        // Masukkan data satu per satu
        foreach ($dataPpi as $data) {
            DB::table('ppis')->insert($data);
        }

        $this->command->info('Berhasil menambahkan 3 data PPI berstatus Disetujui!');
    }
}