<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PerawatanBarang; // Fix: Menggunakan model PerawatanBarang
use App\Notifications\MaintenanceReminderNotification;

class SendMaintenanceReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Nama perintah saat dijalankan di terminal: php artisan maintenance:remind-daily
    protected $signature = 'maintenance:remind-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi pengingat untuk jadwal maintenance yang jatuh tempo hari ini';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hariIni = now()->format('Y-m-d');
        
        $this->info("Mengecek jadwal maintenance untuk tanggal: {$hariIni}");

        // Cari jadwal maintenance yang jatuh tempo hari ini dan belum selesai
        // Fix: Menggunakan model PerawatanBarang
        $jadwalHariIni = PerawatanBarang::with('teknisi')
                                    ->where('tanggal_jadwal', $hariIni)
                                    ->where('status', '!=', 'Selesai')
                                    ->get();

        if ($jadwalHariIni->isEmpty()) {
            $this->info('Tidak ada jadwal maintenance yang jatuh tempo hari ini.');
            return Command::SUCCESS;
        }

        $jumlahNotif = 0;

        foreach ($jadwalHariIni as $task) {
            // Pastikan aset tersebut sudah di-assign ke teknisi tertentu
            if ($task->teknisi) {
                // Kirim notifikasi pengingat ke teknisi terkait
                $task->teknisi->notify(new MaintenanceReminderNotification($task));
                $jumlahNotif++;
            }
        }

        $this->info("Berhasil mengirim {$jumlahNotif} notifikasi pengingat maintenance.");
        
        return Command::SUCCESS;
    }
}