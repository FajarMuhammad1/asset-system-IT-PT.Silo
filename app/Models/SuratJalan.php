<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan';
    
    // Pastikan Primary Key ini sesuai dengan database lo
    protected $primaryKey = 'id_sj'; 

    protected $fillable = [
        'id_suratjalan',
        'no_sj',
        'no_ppi',
        'no_po',
        'tanggal_input',
        'file',
        'jenis_surat_jalan',
        'keterangan',       // Tambahkan ini biar keterangan bisa kesimpan
        'is_bast_submitted' // Checkbox otomatis BAST
    ];

    // --- [RELASI 1] Detail Item di Form Surat Jalan ---
    public function details()
    {
        // Relasi ke tabel surat_jalan_details
        return $this->hasMany(SuratJalanDetail::class, 'surat_jalan_id', 'id_sj');
    }

    // --- [RELASI 2] Relasi ke Stok Aset (Barang Masuk) ---
    // INI YANG BIKIN ERROR KEMARIN, SEKARANG SUDAH ADA.
    public function barangMasuk()
    {
        // Relasi ke tabel barang_masuk
        // 'surat_jalan_id' adalah nama kolom foreign key di tabel barang_masuk
        // 'id_sj' adalah primary key di tabel surat_jalan
        return $this->hasMany(BarangMasuk::class, 'surat_jalan_id', 'id_sj');
    }
}