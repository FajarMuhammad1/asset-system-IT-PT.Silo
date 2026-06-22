<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MaintenanceSchedule;
use App\Models\PerawatanBarang;
use Carbon\Carbon;

class GenerateMaintenanceTickets extends Command
{
    protected $signature = 'maintenance:generate';
    protected $description = 'Otomatis membuat tiket perawatan barang berdasarkan jadwal master';

    public function handle()
    {
        // Cari semua jadwal aktif yang waktunya jatuh pada hari ini atau sudah terlewat
        $schedules = MaintenanceSchedule::where('status', 'aktif')
            ->where('tanggal_next_due', '<=', Carbon::today())
            ->get();

        $count = 0;

        foreach ($schedules as $schedule) {
            // 1. Buat Tiket Kerja untuk Teknisi
            PerawatanBarang::create([
                'maintenance_schedule_id' => $schedule->id,
                'barang_masuk_id'         => $schedule->barang_masuk_id,
                'tanggal_jadwal'          => $schedule->tanggal_next_due,
                'status'                  => 'Menunggu',
            ]);

            // 2. Hitung tanggal jatuh tempo BERIKUTNYA
            $currentNextDue = Carbon::parse($schedule->tanggal_next_due);
            
            if ($schedule->frekuensi === 'mingguan') {
                $newNextDue = $currentNextDue->addWeek();
            } elseif ($schedule->frekuensi === 'bulanan') {
                $newNextDue = $currentNextDue->addMonth();
            } elseif ($schedule->frekuensi === 'tahunan') {
                $newNextDue = $currentNextDue->addYear();
            }

            // 3. Update Master Jadwal untuk periode mendatang
            $schedule->update([
                'tanggal_next_due' => $newNextDue
            ]);

            $count++;
        }

        $this->info("Sukses! {$count} tiket perawatan rutin berhasil di-generate hari ini.");
    }
}