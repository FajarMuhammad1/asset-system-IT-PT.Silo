<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'nama' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'jabatan' => 'Head IT',
            'role' => 'SuperAdmin',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'nama' => 'Admin IT',
            'email' => 'admin@example.com',
            'jabatan' => 'IT Support',
            'role' => 'Admin',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'nama' => 'Teknisi Staff',
            'email' => 'staff@example.com',
            'jabatan' => 'Teknisi',
            'role' => 'Staff',
            'password' => Hash::make('password123'),
        ]);
    }
}
