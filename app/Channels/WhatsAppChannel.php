<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    /**
     * Mengirim notifikasi WhatsApp menggunakan API Gateway (Fonnte)
     */
    public function send($notifiable, Notification $notification)
    {
        // 1. Cek apakah class notifikasi tersebut memiliki fungsi toWhatsApp
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        // 2. Ambil data nomor HP dan isi pesan
        $messageData = $notification->toWhatsApp($notifiable);
        $phone = $messageData['phone'] ?? null;
        $message = $messageData['message'] ?? null;

        // Jika nomor HP atau pesan kosong, jangan kirim apa-apa
        if (!$phone || !$message) {
            return; 
        }

        // 3. Ambil token Fonnte dari file .env
        $tokenFonnte = env('FONNTE_TOKEN'); 

        // 4. [PENGAMAN] Jika token API WA belum diisi di .env (karena belum punya API),
        // sistem tidak akan kirim WA asli dan TIDAK AKAN ERROR. Cukup dicatat di log saja.
        if (empty($tokenFonnte)) {
            Log::info("Simulasi WA Berhasil: Pesan untuk nomor {$phone} sudah siap, tetapi dilewati karena FONNTE_TOKEN masih kosong di .env.");
            return;
        }

        // 5. Jalankan pengiriman asli jika suatu saat nanti token sudah diisi
        try {
            $response = Http::withHeaders([
                'Authorization' => $tokenFonnte,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62', // Otomatis mengubah angka 08 menjadi +62
            ]);

            if (!$response->successful()) {
                Log::error('Fonnte API Error: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Gateway Exception: ' . $e->getMessage());
        }
    }
}