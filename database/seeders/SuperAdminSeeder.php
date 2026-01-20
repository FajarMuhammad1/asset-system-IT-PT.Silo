<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'nik' => 'SA-999',
            'nama' => 'Super Admin IT',
            'email' => 'headit@mail.com',
            'jabatan' => 'IT Manager', // Jabatan ini yang tampil di surat
            'departemen' => 'Information Technology',
            'perusahaan' => 'Head Office',
            'role' => 'SuperAdmin', // Role Kunci
            'status' => 'Aktif',
            'password' => Hash::make('password123'), // Ganti password sesuai keinginan
        ]);
    }
}