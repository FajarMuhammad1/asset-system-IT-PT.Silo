<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// HAPUS baris 'use App\Models\Kategori;' karena gak dipake

class MasterBarang extends Model
{
    protected $table = 'master_barang';

    // Pastikan 'kategori' masuk sini sebagai string biasa
    protected $fillable = ['nama_barang', 'kategori', 'merk', 'spesifikasi'];

    // --- HAPUS FUNGSI public function kategori() { ... } ---
    // Karena kategori cuma kolom biasa, bukan relasi.

    public function suratJalanDetails() {
        return $this->hasMany(SuratJalanDetail::class, 'master_barang_id');
    }

    public function assets() {
        return $this->hasMany(BarangMasuk::class, 'master_barang_id');
    }
}