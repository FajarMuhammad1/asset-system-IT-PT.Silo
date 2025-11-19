<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity; // <-- 1. Import (dari kode baru lo)
use Spatie\Activitylog\LogOptions;           // <-- 1. Import (dari kode baru lo)

class Ppi extends Model
{
    use HasFactory, LogsActivity; // <-- 2. Gabung trait-nya

    /**
     * Ini dari kode lama lo (JANGAN HAPUS)
     */
    protected $table = 'ppis';

    protected $fillable = [
        'no_ppi', 'tanggal', 'user_id', 
        'perangkat', 'ba_kerusakan', 'keterangan', 
        'file_ppi', 'status'
    ];

    /**
     * Ini relasi lama lo (JANGAN HAPUS)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ini fungsi Activity Log baru lo (PENTING)
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Catet semua yg di $fillable
            // Deskripsinya gw bikin lebih jelas
            ->setDescriptionForEvent(fn(string $eventName) => "PPI (Request) telah di-{$eventName}");
    }
}