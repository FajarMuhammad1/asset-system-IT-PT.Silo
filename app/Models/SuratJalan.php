<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    protected $table = 'surat_jalan';
    protected $primaryKey = 'id_sj'; // <-- Kalo PK lo 'id_sj'

    // HAPUS SEMUA SAMPAH DARI FILLABLE LO
   protected $fillable = [
    'id_suratjalan',
    'no_sj',
    'no_ppi',
    'no_po',
    'tanggal_input',
    'file',
    'jenis_surat_jalan',
    'is_bast_submitted' // <-- GANTI PAKE INI
];

    // INI RELASI BARUNYA (KE "LIST" BARANG)
    public function details()
    {
        // 1 SJ 'punya banyak' (hasMany) detail
        return $this->hasMany(SuratJalanDetail::class, 'surat_jalan_id', 'id_sj');
    }
}