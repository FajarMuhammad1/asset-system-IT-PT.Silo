<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaOperasional extends Model
{
    use HasFactory;

    protected $table = 'biaya_operasional';

    protected $fillable = [
        'ticket_id',
        'staff_id',
        'diberikan_oleh',
        'nominal',
        'keterangan',
        'tanggal_pemberian',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_pemberian' => 'date',
    ];

    // Relasi ke Tiket Helpdesk
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    // Relasi ke Staff yang menerima bonus
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // Relasi ke Admin yang memberikan bonus
    public function pemberi()
    {
        return $this->belongsTo(User::class, 'diberikan_oleh');
    }
}
