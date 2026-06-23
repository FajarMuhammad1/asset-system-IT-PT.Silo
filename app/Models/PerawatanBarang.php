<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerawatanBarang extends Model
{
    // 1. Definisikan nama tabel secara eksplisit agar lebih aman
    protected $table = 'perawatan_barangs';

    // 2. Izinkan semua kolom diisi massal kecuali ID
    protected $guarded = ['id'];

    // 3. Konversi kolom tanggal agar otomatis menjadi instance Carbon
    protected $casts = [
        'tanggal_jadwal' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // ==========================================
    // RELASI DATABASE
    // ==========================================

    /**
     * Relasi ke data aset fisik / barang masuk
     */
    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }

    /**
     * Relasi ke user/staff yang bertugas sebagai teknisi
     */
    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    /**
     * Relasi ke jadwal induk / aturan rutin (jika tiket ini digenerate dari jadwal)
     */
    public function schedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_schedule_id');
    }
}