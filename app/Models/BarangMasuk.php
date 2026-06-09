<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// Pastikan Anda memanggil model yang dibutuhkan
use App\Models\Ticket;
use App\Models\SuratJalan;
use App\Models\User; // Asumsi pemegang menggunakan model User atau sesuaikan dengan model Anda (misal: Pegawai/Karyawan)

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk'; 

    // ... [property $fillable Anda] ...

    public function masterBarang()
    {
        return $this->belongsTo(MasterBarang::class, 'master_barang_id');
    }

    public function barangKeluar() {
        return $this->hasMany(BarangKeluar::class, 'barang_masuk_id');
    }

    public function mutasiAssets() {
        return $this->hasMany(MutasiAsset::class, 'barang_masuk_id');
    }

    /**
     * Relasi ke Tiket Perbaikan (Helpdesk)
     * UPDATE: Menggunakan model Ticket::class
     */
    public function helpdesk() {
        return $this->hasMany(Ticket::class, 'barang_masuk_id');
    }

    public function disposal() {
        return $this->hasOne(Disposal::class, 'barang_masuk_id');
    }

    // --- TAMBAHAN BARU UNTUK MENGATASI ERROR ---

    /**
     * Relasi ke model SuratJalan
     */
    public function suratJalan()
    {
        // Asumsi relasi belongsTo (Barang Masuk mencatat ID Surat Jalan)
        // Sesuaikan nama kolom foreign key 'surat_jalan_id' jika di database Anda berbeda
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }

    /**
     * Relasi ke pemegang aset
     */
    public function pemegang()
    {
        // Asumsi pemegang aset berelasi dengan tabel users
        // Sesuaikan model User::class dan kolom 'pemegang_id' dengan struktur database Anda
        return $this->belongsTo(User::class, 'pemegang_id'); 
    }
}