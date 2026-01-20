<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppis', function (Blueprint $table) {
            // 1. TTD Pengguna (User yang minta)
            if (!Schema::hasColumn('ppis', 'ttd_pemohon')) {
                $table->longText('ttd_pemohon')->nullable()->after('keterangan'); 
            }

            // 2. TTD SuperAdmin (Sebagai Approval)
            // Kita namakan 'ttd_superadmin' biar jelas
            if (!Schema::hasColumn('ppis', 'ttd_superadmin')) {
                $table->longText('ttd_superadmin')->nullable()->after('ttd_pemohon');
            }

            // 3. Alasan Tolak & Tanggal
            if (!Schema::hasColumn('ppis', 'alasan_tolak')) {
                $table->text('alasan_tolak')->nullable()->after('ttd_superadmin');
            }
            if (!Schema::hasColumn('ppis', 'tgl_approve')) {
                $table->dateTime('tgl_approve')->nullable()->after('alasan_tolak');
            }
        });

        // 4. STATUS FLOW (Penting!)
        // Kita tambah 'pending_superadmin' artinya status saat menunggu approval SuperAdmin
        DB::statement("ALTER TABLE ppis MODIFY COLUMN status ENUM('pending', 'pending_superadmin', 'disetujui', 'ditolak', 'selesai') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Rollback...
    }
};