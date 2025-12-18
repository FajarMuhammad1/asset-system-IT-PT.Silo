<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Password default: 12345678
        $password = Hash::make('12345678');
        $now = Carbon::now();

        $users = [
            [
                'nik'        => 'KRY2025001',
                'nama'       => 'Budi Santoso',
                'email'      => 'budi@mail.com', // Lebih simpel
                'jabatan'    => 'Staff Admin',
                'departemen' => 'Operation',
                'perusahaan' => 'PT.SFP',
                'role'       => 'Pengguna',
                'status'     => 'Aktif',
                'password'   => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik'        => 'KRY2025002',
                'nama'       => 'Siti Aminah',
                'email'      => 'siti@mail.com',
                'jabatan'    => 'Staff Finance',
                'departemen' => 'Finance',
                'perusahaan' => 'PT.BCI',
                'role'       => 'Pengguna',
                'status'     => 'Aktif',
                'password'   => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik'        => 'KRY2025003',
                'nama'       => 'Doni Pratama',
                'email'      => 'doni@mail.com',
                'jabatan'    => 'Operator',
                'departemen' => 'Produksi',
                'perusahaan' => 'PT.SILO',
                'role'       => 'Pengguna',
                'status'     => 'Aktif',
                'password'   => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik'        => 'KRY2025004',
                'nama'       => 'Rina Kartika',
                'email'      => 'rina@mail.com',
                'jabatan'    => 'Staff HRD',
                'departemen' => 'HRD',
                'perusahaan' => 'PT.Banjar Asri',
                'role'       => 'Pengguna',
                'status'     => 'Aktif',
                'password'   => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik'        => 'KRY2025005',
                'nama'       => 'Eko Wahyudi',
                'email'      => 'eko@mail.com',
                'jabatan'    => 'Supervisor',
                'departemen' => 'Warehouse',
                'perusahaan' => 'PT.SFP',
                'role'       => 'Pengguna',
                'status'     => 'Aktif',
                'password'   => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik'        => 'KRY2025006',
                'nama'       => 'Maya Suryani',
                'email'      => 'maya@mail.com',
                'jabatan'    => 'Receptionist',
                'departemen' => 'General Affair',
                'perusahaan' => 'PT.BCI',
                'role'       => 'Pengguna',
                'status'     => 'Aktif',
                'password'   => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik'        => 'KRY2025007',
                'nama'       => 'Agus Setiawan',
                'email'      => 'agus@mail.com',
                'jabatan'    => 'Staff Logistik',
                'departemen' => 'Logistik',
                'perusahaan' => 'PT.SILO',
                'role'       => 'Pengguna',
                'status'     => 'Aktif',
                'password'   => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik'        => 'KRY2025008',
                'nama'       => 'Dewi Lestari',
                'email'      => 'dewi@mail.com',
                'jabatan'    => 'Staff Purchasing',
                'departemen' => 'Purchasing',
                'perusahaan' => 'PT.SFP',
                'role'       => 'Pengguna',
                'status'     => 'Aktif',
                'password'   => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik'        => 'KRY2025009',
                'nama'       => 'Reza Rahardian',
                'email'      => 'reza@mail.com',
                'jabatan'    => 'Surveyor',
                'departemen' => 'Mine Survey',
                'perusahaan' => 'PT.BCI',
                'role'       => 'Pengguna',
                'status'     => 'Aktif',
                'password'   => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik'        => 'KRY2025010',
                'nama'       => 'Fajar Nugroho',
                'email'      => 'fajar@mail.com',
                'jabatan'    => 'Safety Officer',
                'departemen' => 'HSE',
                'perusahaan' => 'PT.Banjar Asri',
                'role'       => 'Pengguna',
                'status'     => 'Aktif',
                'password'   => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('users')->insert($users);
    }
}