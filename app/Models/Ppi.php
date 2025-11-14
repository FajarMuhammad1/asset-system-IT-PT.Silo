<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ppi extends Model
{
    use HasFactory;

    protected $table = 'ppis';

    protected $fillable = [
        'no_ppi', 'tanggal', 'user_id', 
        'perangkat', 'ba_kerusakan', 'keterangan', 
        'file_ppi', 'status'
    ];

    // Relasi: 1 PPI dimiliki 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}