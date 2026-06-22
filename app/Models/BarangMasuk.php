<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// Pastikan Anda memanggil model yang dibutuhkan
use App\Models\Ticket;
use App\Models\SuratJalan;
use App\Models\User; // Asumsi pemegang menggunakan model User atau sesuaikan dengan model Anda
use App\Models\MaintenanceSchedule; // Tambahan untuk Maintenance
use App\Models\PerawatanBarang;     // Tambahan untuk Maintenance

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

    /**
     * Relasi ke model SuratJalan
     */
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }

    /**
     * Relasi ke pemegang aset
     */
    public function pemegang()
    {
        return $this->belongsTo(User::class, 'pemegang_id'); 
    }

    // =========================================================================
    // --- TAMBAHAN BARU UNTUK FITUR MAINTENANCE JADWAL & TIKET PERAWATAN ---
    // =========================================================================

    /**
     * Relasi ke Master Jadwal Perawatan rutin (Mingguan/Bulanan/Tahunan)
     */
    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class, 'barang_masuk_id');
    }

    /**
     * Relasi ke lembar kerja / Tiket Pengerjaan Perawatan oleh Teknisi
     */
    public function perawatanBarangs()
    {
        return $this->hasMany(PerawatanBarang::class, 'barang_masuk_id');
    }
}