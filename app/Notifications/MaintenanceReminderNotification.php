<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\AnonymousNotifiable; // Wajib untuk routing grup eksternal
use App\Channels\WhatsAppChannel; // [BARU] Import Custom Channel WhatsApp Abang

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
     * Tentukan pintu pengiriman notifikasi (Pilih: database, telegram, whatsapp)
     */
    public function via(object $notifiable): array
    {
        // 1. Jika ini pengiriman tanpa nama/user (Sistem Broadcast via Controller)
        if ($notifiable instanceof AnonymousNotifiable) {
            $channels = [];

            // Jika route telegram terkonfigurasi, daftarkan jalurnya
            if ($notifiable->routeNotificationFor('telegram')) {
                $channels[] = TelegramChannel::class;
            }

            // [BARU] Jika route whatsapp terkonfigurasi, daftarkan jalurnya
            if ($notifiable->routeNotificationFor(WhatsAppChannel::class)) {
                $channels[] = WhatsAppChannel::class;
            }

            return $channels;
        }

        // 2. Jika dikirim ke User biasa (untuk masuk ke lonceng dashboard web)
        return ['database'];
    }

    /**
     * FORMAT NOTIFIKASI TELEGRAM (MASUK KE GRUP)
     */
    public function toTelegram($notifiable)
    {
        $namaPekerjaan = $this->task->keterangan ?? 'Maintenance Rutin Aset';
        $status = $this->task->status ?? 'Menunggu';

        return TelegramMessage::create()
            ->to(env('TELEGRAM_GROUP_ID')) // Tembak ke ID grup di .env
            ->content("📢 *TUGAS MAINTENANCE BERSAMA (TELEGRAM)*\n\n" .
                      "Halo Tim IT, ada jadwal maintenance terbuka yang perlu dikerjakan hari ini.\n\n" .
                      "▪️ *Pekerjaan:* " . $namaPekerjaan . "\n" .
                      "▪️ *Status:* " . $status . "\n\n" .
                      "Siapa yang standby mohon segera dieksekusi ya! 💪")
            ->button('Buka Halaman Tugas', route('staff.maintenance.index'));
    }

    /**
     * [BARU] FORMAT NOTIFIKASI WHATSAPP
     * Menghasilkan struktur array sesuai kebutuhan WhatsAppChannel.php milik Abang
     */
    public function toWhatsApp($notifiable)
    {
        $namaPekerjaan = $this->task->keterangan ?? 'Maintenance Rutin Aset';
        $status = $this->task->status ?? 'Menunggu';

        // Susun teks pesan WhatsApp (Menggunakan bintang '*' untuk cetak tebal)
        $pesan = "📢 *TUGAS MAINTENANCE BERSAMA (WHATSAPP)*\n\n" .
                 "Halo Tim IT, ada jadwal maintenance terbuka baru hari ini.\n\n" .
                 "▪️ *Pekerjaan:* " . $namaPekerjaan . "\n" .
                 "▪️ *Status:* " . $status . "\n\n" .
                 "🔗 *Link Tugas:* " . route('staff.maintenance.index') . "\n\n" .
                 "Siapa yang standby mohon segera dieksekusi ya! 💪";

        // Mengembalikan data array yang nantinya dibaca $notification->toWhatsApp($notifiable)
        return [
            'phone'   => $notifiable->routeNotificationFor(WhatsAppChannel::class) ?? env('WHATSAPP_TARGET'),
            'message' => $pesan
        ];
    }

    /**
     * FORMAT NOTIFIKASI EMAIL
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
     * FORMAT NOTIFIKASI IN-APP (DATABASE)
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