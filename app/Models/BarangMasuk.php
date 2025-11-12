<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';

    // INI FILLABLE BARUNYA (SESUAI MIGRATION)
    protected $fillable = [
        'surat_jalan_id',
        'master_barang_id',
        'serial_number',
        'kode_asset',
        'status',
        'user_pemegang_id',
        'tanggal_masuk', // (Ini jangan lupa, kayaknya ketinggalan di migrasi)
        'keterangan'
    ];

    // Relasi ke Master Barang
    public function masterBarang()
    {
        return $this->belongsTo(MasterBarang::class, 'master_barang_id');
    }
    
    // Relasi ke Pemegang
    public function pemegang()
    {
        return $this->belongsTo(User::class, 'user_pemegang_id');
    }

    // Relasi ke SJ
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id', 'id_sj');
    }
}