<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $task;

    /**
     * Menerima data jadwal maintenance dari controller/command
     */
    public function __construct($task)
    {
        $this->task = $task;
    }

    /**
     * Tentukan pintu pengiriman notifikasi (Pilih: mail, database, telegram)
     */
    public function via(object $notifiable): array
    {
        // Mengirim notifikasi ke In-app dashboard (database) dan Email (mail)
        return ['database', 'mail'];
    }

    /**
     * 1. FORMAT NOTIFIKASI EMAIL
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Catatan: Sesuaikan nama kolom ('keterangan', 'lokasi', dll) dengan yang ada di tabel Anda
        $namaPekerjaan = $this->task->keterangan ?? 'Maintenance Rutin Aset';

        return (new MailMessage)
                    ->subject('🔔 Pengingat: Jadwal Maintenance Hari Ini')
                    ->greeting('Halo, ' . $notifiable->name)
                    ->line('Ini adalah pengingat otomatis bahwa Anda memiliki jadwal maintenance hari ini.')
                    ->line('Detail Pekerjaan: ' . $namaPekerjaan)
                    ->line('Status Saat Ini: ' . $this->task->status)
                    // Sesuaikan route di bawah dengan route detail tugas teknisi Anda
                    ->action('Buka Halaman Tugas', route('staff.maintenance.index'))
                    ->line('Mohon segera dieksekusi agar aset tetap dalam kondisi prima. Terima kasih!');
    }

    /**
     * 2. FORMAT NOTIFIKASI IN-APP (DATABASE)
     */
    public function toArray(object $notifiable): array
    {
        $namaPekerjaan = $this->task->keterangan ?? 'Maintenance Rutin Aset';

        return [
            'maintenance_id' => $this->task->id,
            'judul'          => 'Tugas Maintenance Hari Ini',
            'pesan'          => 'Jangan lupa untuk mengeksekusi jadwal: ' . $namaPekerjaan,
            // Sesuaikan route link tujuan saat notifikasi di klik
            'link'           => route('staff.maintenance.index') 
        ];
    }
}