<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class TicketAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;

    /**
     * Masukkan data tiket yang mau dikirim
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Tentukan channel pengiriman di sini (Sudah dipasang sensor pengaman ganda)
     */
    public function via($notifiable)
    {
        // 1. Channel default yang wajib dieksekusi (Lonceng Dashboard & Email)
        $channels = ['database', 'mail'];

        // 2. SENSOR WHATSAPP: Hanya kirim WA jika teknisi memiliki nomor HP di database
        // Pastikan kolom 'no_hp' di bawah ini sesuai dengan nama kolom di tabel 'users' Anda (misal: 'phone' atau 'no_telp')
        if (!empty($notifiable->no_hp)) {
            $channels[] = \App\Channels\WhatsAppChannel::class;
        }

        // 3. SENSOR TELEGRAM: Hanya kirim ke Telegram jika teknisi memiliki ID Telegram di database
        if (!empty($notifiable->telegram_chat_id)) {
            $channels[] = TelegramChannel::class;
        }

        return $channels;
    }

    /**
     * 1. FORMAT NOTIFIKASI IN-APP (DASHBOARD)
     */
    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'judul'     => 'Tugas Tiket Baru!',
            'pesan'     => 'Anda ditugaskan untuk memperbaiki: ' . $this->ticket->judul_masalah,
            'link'      => route('staff.helpdesk.show', $this->ticket->id)
        ];
    }

    /**
     * 2. FORMAT NOTIFIKASI EMAIL
     */
    public function toMail($notifiable)
    {
        // Menggunakan fallback nama jika $notifiable->nama kosong (disesuaikan menjadi $notifiable->name jika pakai default Laravel)
        $namaUser = $notifiable->nama ?? $notifiable->name ?? 'Teknisi';

        return (new MailMessage)
                    ->subject('New Task: ' . $this->ticket->judul_masalah)
                    ->greeting('Halo, ' . $namaUser)
                    ->line('Anda telah ditugaskan untuk menangani tiket helpdesk baru.')
                    ->line('Masalah: ' . $this->ticket->judul_masalah)
                    ->action('Lihat Detail Tiket', route('staff.helpdesk.show', $this->ticket->id))
                    ->line('Mohon untuk segera melakukan respon pada tiket ini.');
    }

    /**
     * 3. FORMAT NOTIFIKASI TELEGRAM (PERSONAL CHAT KE TEKNISI)
     */
    public function toTelegram($notifiable)
    {
        $chatId = $notifiable->telegram_chat_id; 
        $namaUser = $notifiable->nama ?? $notifiable->name ?? 'Teknisi';
        
        return TelegramMessage::create()
            ->to($chatId)
            ->content("📢 *TUGAS TIKET BARU (TELEGRAM)*\n\n" .
                      "Halo " . $namaUser . ",\n" .
                      "Ada aset yang butuh penanganan Anda:\n\n" .
                      "📌 *No Tiket:* " . ($this->ticket->no_tiket ?? '-') . "\n" .
                      "🚨 *Prioritas:* " . ($this->ticket->prioritas ?? '-') . "\n" .
                      "📝 *Masalah:* " . $this->ticket->judul_masalah . "\n" .
                      "📍 *Lokasi:* " . ($this->ticket->lokasi ?? '-') . "\n\n" .
                      "Silakan cek dashboard staff Anda.")
            ->button('Buka Aplikasi', route('staff.helpdesk.show', $this->ticket->id));
    }

    /**
     * 4. FORMAT NOTIFIKASI WHATSAPP (PERSONAL CHAT KE TEKNISI VIA FONNTE)
     */
    public function toWhatsApp($notifiable)
    {
        $noWaTeknisi = $notifiable->no_hp; 
        $namaUser = $notifiable->nama ?? $notifiable->name ?? 'Teknisi';

        // Rakit pesan yang akan dikirim via WA
        $pesanWa = "📢 *TUGAS TIKET BARU (WHATSAPP)*\n\n";
        $pesanWa .= "Halo *" . $namaUser . "*,\n";
        $pesanWa .= "Anda baru saja ditugaskan untuk menangani tiket helpdesk baru.\n\n";
        $pesanWa .= "📌 *No Tiket:* " . ($this->ticket->no_tiket ?? '-') . "\n";
        $pesanWa .= "🚨 *Prioritas:* " . ($this->ticket->prioritas ?? '-') . "\n";
        $pesanWa .= "📝 *Masalah:* " . $this->ticket->judul_masalah . "\n";
        $pesanWa .= "📍 *Lokasi:* " . ($this->ticket->lokasi ?? '-') . "\n\n";
        $pesanWa .= "Silakan login ke aplikasi untuk melihat detail dan mengubah status perbaikan:\n";
        $pesanWa .= route('staff.helpdesk.show', $this->ticket->id);

        return [
            'phone'   => $noWaTeknisi,
            'message' => $pesanWa
        ];
    }
}