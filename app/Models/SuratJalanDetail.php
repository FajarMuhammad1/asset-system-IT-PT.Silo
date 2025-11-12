<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SuratJalanDetail extends Model
{
    protected $table = 'surat_jalan_details';
    protected $fillable = ['surat_jalan_id', 'master_barang_id', 'qty'];

    // 1 Detail "dimiliki oleh" 1 Header SJ
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id', 'id_sj');
    }

    // 1 Detail "nunjuk ke" 1 Master Barang
    public function masterBarang()
    {
        return $this->belongsTo(MasterBarang::class, 'master_barang_id');
    }
}