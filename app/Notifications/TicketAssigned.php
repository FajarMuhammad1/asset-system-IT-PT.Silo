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

    // Masukkan data tiket yang mau dikirim
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    // Tentukan channel pengiriman di sini (Sudah dipasang sensor pengaman)
    public function via($notifiable)
    {
        // 1. Channel default yang wajib dieksekusi
        $channels = ['database', 'mail', \App\Channels\WhatsAppChannel::class];

        // 2. SENSOR OTOMATIS: Hanya kirim ke Telegram jika teknisi memiliki ID Telegram di database
        if (!empty($notifiable->telegram_chat_id)) {
            $channels[] = TelegramChannel::class;
        }

        return $channels;
    }

    // 1. FORMAT NOTIFIKASI IN-APP (DASHBOARD)
    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'judul'     => 'Tugas Tiket Baru!',
            'pesan'     => 'Anda ditugaskan untuk memperbaiki: ' . $this->ticket->judul_masalah,
            'link'      => route('staff.helpdesk.show', $this->ticket->id)
        ];
    }

    // 2. FORMAT NOTIFIKASI EMAIL
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Task: ' . $this->ticket->judul_masalah)
                    ->greeting('Halo, ' . $notifiable->nama)
                    ->line('Anda telah ditugaskan untuk menangani tiket helpdesk baru.')
                    ->line('Masalah: ' . $this->ticket->judul_masalah)
                    ->action('Lihat Detail Tiket', route('staff.helpdesk.show', $this->ticket->id))
                    ->line('Mohon untuk segera melakukan respon pada tiket ini.');
    }

    // 3. FORMAT NOTIFIKASI TELEGRAM
    public function toTelegram($notifiable)
    {
        $chatId = $notifiable->telegram_chat_id; 
        
        return TelegramMessage::create()
            ->to($chatId)
            ->content("*TUGAS TIKET BARU!*\n\n" .
                      "Halo " . $notifiable->nama . ",\n" .
                      "Ada aset yang butuh penanganan Anda:\n\n" .
                      "📌 *Masalah:* " . $this->ticket->judul_masalah . "\n" .
                      "📍 *Lokasi:* " . $this->ticket->lokasi . "\n\n" .
                      "Silakan cek dashboard staff Anda.")
            ->button('Buka Aplikasi', route('staff.helpdesk.show', $this->ticket->id));
    }

    // 4. FORMAT NOTIFIKASI WHATSAPP (BARU)
    public function toWhatsApp($notifiable)
    {
        // Sesuaikan 'no_hp' dengan nama kolom nomor telepon di tabel users Anda
        $noWaTeknisi = $notifiable->no_hp; 

        // Rakit pesan yang akan dikirim via WA
        $pesanWa = "*TUGAS TIKET BARU!*\n\n";
        $pesanWa .= "Halo *" . $notifiable->nama . "*,\n";
        $pesanWa .= "Anda baru saja ditugaskan untuk menangani tiket helpdesk baru.\n\n";
        $pesanWa .= "📌 *No Tiket:* " . $this->ticket->no_tiket . "\n";
        $pesanWa .= "🚨 *Prioritas:* " . $this->ticket->prioritas . "\n";
        $pesanWa .= "📝 *Masalah:* " . $this->ticket->judul_masalah . "\n\n";
        $pesanWa .= "Silakan login ke aplikasi untuk melihat detail dan mengubah status perbaikan:\n";
        $pesanWa .= route('staff.helpdesk.show', $this->ticket->id);

        return [
            'phone'   => $noWaTeknisi,
            'message' => $pesanWa
        ];
    }
}