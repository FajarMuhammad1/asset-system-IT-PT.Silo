<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterBarangSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // Kosongkan tabel master_barang dulu (Opsional)
        // DB::table('master_barang')->truncate();

        $barang = [
            // --- LAPTOP & PC ---
            [
                'nama_barang' => 'Laptop Lenovo Thinkpad',
                'kategori'    => 'Laptop',
                'merk'        => 'Lenovo',
                'spesifikasi' => 'Core i5 Gen 11, RAM 8GB, SSD 512GB',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],
            [
                'nama_barang' => 'PC Desktop Dell',
                'kategori'    => 'PC Desktop',
                'merk'        => 'Dell',
                'spesifikasi' => 'Optiplex 3080, Core i7, RAM 16GB',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],
            [
                'nama_barang' => 'Laptop Dell Latitude',
                'kategori'    => 'Laptop',
                'merk'        => 'Dell',
                'spesifikasi' => 'Series 5000, Core i5, RAM 16GB',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],

            // --- PRINTER & SCANNER ---
            [
                'nama_barang' => 'Printer Epson L3110',
                'kategori'    => 'Printer',
                'merk'        => 'Epson',
                'spesifikasi' => 'EcoTank All-in-One Ink Tank Printer',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],
            [
                'nama_barang' => 'Printer Dot Matrix LX-310',
                'kategori'    => 'Printer',
                'merk'        => 'Epson',
                'spesifikasi' => '9 Pin, Impact Dot Matrix',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],
            [
                'nama_barang' => 'Scanner Canon',
                'kategori'    => 'Scanner',
                'merk'        => 'Canon',
                'spesifikasi' => 'Lide 400 Flatbed Scanner',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],

            // --- PERIPHERAL & AKSESORIS ---
            [
                'nama_barang' => 'Mouse Wireless',
                'kategori'    => 'Aksesoris',
                'merk'        => 'Logitech',
                'spesifikasi' => 'M170 Wireless 2.4Ghz',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],
            [
                'nama_barang' => 'Keyboard Mechanical',
                'kategori'    => 'Aksesoris',
                'merk'        => 'Logitech',
                'spesifikasi' => 'K845 Mechanical Illuminated',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],
            [
                'nama_barang' => 'Monitor Samsung 24"',
                'kategori'    => 'Monitor',
                'merk'        => 'Samsung',
                'spesifikasi' => 'LED IPS Panel 75Hz',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],
            [
                'nama_barang' => 'UPS Prolink',
                'kategori'    => 'UPS',
                'merk'        => 'Prolink',
                'spesifikasi' => '1200VA Line Interactive',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],

            // --- NETWORK ---
            [
                'nama_barang' => 'Kabel LAN Belden',
                'kategori'    => 'Network',
                'merk'        => 'Belden',
                'spesifikasi' => 'Cat 6 Original (Per Box)',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],
            [
                'nama_barang' => 'Radio Rig / HT',
                'kategori'    => 'Network',
                'merk'        => 'Icom',
                'spesifikasi' => 'IC-2300H VHF FM Transceiver',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],
            [
                'nama_barang' => 'CCTV Outdoor',
                'kategori'    => 'CCTV',
                'merk'        => 'Hikvision',
                'spesifikasi' => '2MP Bullet Camera IP67',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],

            // --- CONSUMABLE ---
            [
                'nama_barang' => 'Pita Printer LX-310',
                'kategori'    => 'Consumable',
                'merk'        => 'Epson',
                'spesifikasi' => 'Ribbon Cartridge Original',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],
            [
                'nama_barang' => 'Tinta Epson 003 Black',
                'kategori'    => 'Consumable',
                'merk'        => 'Epson',
                'spesifikasi' => 'Botol 65ml',
                'created_at'  => $now, 
                'updated_at'  => $now,
            ],
        ];

        DB::table('master_barang')->insert($barang);
    }
}