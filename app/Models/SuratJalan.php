<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan';
    
    // Primary Key Custom (sesuai database kamu)
    protected $primaryKey = 'id_sj'; 

    protected $fillable = [
        'id_suratjalan',
        'no_sj',
        'no_ppi',       // Asumsi: Ini Foreign Key ke tabel PPI
        'no_po',
        'tanggal_input',
        'file',
        'jenis_surat_jalan',
        'keterangan',       
        'is_bast_submitted' 
    ];

    /**
     * RELASI 1: Detail Item (Untuk Form Surat Jalan)
     */
    public function details()
    {
        return $this->hasMany(SuratJalanDetail::class, 'surat_jalan_id', 'id_sj');
    }

    /**
     * RELASI 2: Stok Aset (Barang Masuk)
     * Digunakan untuk melihat barang apa saja yang masuk lewat SJ ini
     */
    public function barangMasuk()
    {
        // 'surat_jalan_id' = Foreign Key di tabel barang_masuk
        // 'id_sj' = Primary Key di tabel surat_jalan
        return $this->hasMany(BarangMasuk::class, 'surat_jalan_id', 'id_sj');
    }

    /**
     * RELASI 3: PPI (Permintaan) - PENYEBAB ERROR KEMARIN
     * Menghubungkan Surat Jalan ke Data PPI
     */
    public function ppi()
    {
        // Parameter 2 ('no_ppi'): Nama kolom di tabel surat_jalan yang menyimpan ID PPI
        // Parameter 3 ('id'): Nama Primary Key di tabel ppi (biasanya 'id')
        
        // PENTING: Jika di database kolomnya bernama 'ppi_id', ganti 'no_ppi' jadi 'ppi_id'
        return $this->belongsTo(Ppi::class, 'no_ppi', 'id');
    }
}