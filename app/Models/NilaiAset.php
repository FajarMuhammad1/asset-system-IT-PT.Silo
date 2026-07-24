<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAset extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     * Secara default Laravel akan mencari tabel berakhiran 's' (nilai_asets).
     * Karena nama tabel kita 'nilai_aset', maka harus didefinisikan secara eksplisit.
     *
     * @var string
     */
    protected $table = 'nilai_aset';

    /**
     * Kolom yang dapat diisi melalui mass assignment (fillable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'barang_masuk_id',
        'nilai_awal',
        'nilai_sekarang',
        'tanggal_penilaian',
        'metode_depresiasi',
        'masa_manfaat_bulan',
        'keterangan',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     * Membantu Laravel mengonversi tipe data secara otomatis saat diakses.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_penilaian' => 'date',
        'nilai_awal' => 'decimal:2',
        'nilai_sekarang' => 'decimal:2',
        'masa_manfaat_bulan' => 'integer',
    ];

    /**
     * Hubungan Relasi (Belongs To)
     * Mengindikasikan bahwa setiap Nilai Aset dimiliki oleh satu Barang Masuk.
     */
    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }
}