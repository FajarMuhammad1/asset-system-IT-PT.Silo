<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
// Hapus ShouldQueue agar notifikasi langsung jalan saat testing
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\AnonymousNotifiable; // <-- [BARU] Wajib untuk ngirim ke Grup

// Import class untuk Telegram
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class MaintenanceReminderNotification extends Notification 
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
        // 1. Jika ini pengiriman tanpa nama user (untuk tembak ke Grup Telegram)
        if ($notifiable instanceof AnonymousNotifiable) {
            return [TelegramChannel::class];
        }

        // 2. Jika dikirim ke User biasa (untuk masuk ke lonceng web)
        return ['database'];
    }

    /**
     * [BARU] FORMAT NOTIFIKASI TELEGRAM (MASUK KE GRUP)
     */
    public function toTelegram($notifiable)
    {
        $namaPekerjaan = $this->task->keterangan ?? 'Maintenance Rutin Aset';
        $status = $this->task->status ?? 'Menunggu';

        return TelegramMessage::create()
            ->to(env('TELEGRAM_GROUP_ID')) // <-- Tembak ke ID grup di .env
            ->content("📢 *TUGAS MAINTENANCE BERSAMA*\n\n" .
                      "Halo Tim IT, ada jadwal maintenance terbuka yang perlu dikerjakan hari ini.\n\n" .
                      "▪️ *Pekerjaan:* " . $namaPekerjaan . "\n" .
                      "▪️ *Status:* " . $status . "\n\n" .
                      "Siapa yang standby mohon segera dieksekusi ya! 💪")
            ->button('Buka Halaman Tugas', route('staff.maintenance.index'));
    }

    /**
     * 1. FORMAT NOTIFIKASI EMAIL
     */
    public function toMail(object $notifiable): MailMessage
    {
        $namaPekerjaan = $this->task->keterangan ?? 'Maintenance Rutin Aset';

        return (new MailMessage)
                    ->subject('🔔 Pengingat: Jadwal Maintenance Hari Ini')
                    ->greeting('Halo, ' . ($notifiable->nama ?? 'Tim')) 
                    ->line('Ini adalah pengingat otomatis bahwa ada jadwal maintenance hari ini.')
                    ->line('Detail Pekerjaan: ' . $namaPekerjaan)
                    ->line('Status Saat Ini: ' . $this->task->status)
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
            'judul'          => 'Tugas Maintenance Baru',
            'pesan'          => 'Jadwal terbuka: ' . $namaPekerjaan,
            'link'           => route('staff.maintenance.index') 
        ];
    }
}