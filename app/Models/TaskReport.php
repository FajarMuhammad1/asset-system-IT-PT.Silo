<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskReport extends Model
{
    use HasFactory;

    protected $table = 'task_reports';

    protected $fillable = [
        'staff_id',
        'ticket_id',
        'judul',
        'deskripsi',
        'hasil',
        'lampiran',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    // Relasi ke Staff (User)
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    // Relasi ke Tiket (Opsional)   
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}