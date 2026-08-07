<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mutasi_assets', function (Blueprint $table) {
            if (!Schema::hasColumn('mutasi_assets', 'pemohon_id')) {
                $table->foreignId('pemohon_id')
                      ->nullable()
                      ->after('user_tujuan_id')
                      ->constrained('users')
                      ->onDelete('set null');
            }

            if (!Schema::hasColumn('mutasi_assets', 'approved_by_id')) {
                $table->foreignId('approved_by_id')
                      ->nullable()
                      ->after('pemohon_id')
                      ->constrained('users')
                      ->onDelete('set null');
            }

            if (!Schema::hasColumn('mutasi_assets', 'status')) {
                $table->string('status')
                      ->default('Menunggu Approval Manager')
                      ->after('approved_by_id');
            }

            if (!Schema::hasColumn('mutasi_assets', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')
                      ->nullable()
                      ->after('status');
            }

            if (!Schema::hasColumn('mutasi_assets', 'lokasi_baru')) {
                $table->string('lokasi_baru')
                      ->nullable()
                      ->after('alasan_penolakan');
            }

            if (!Schema::hasColumn('mutasi_assets', 'log_serah_terima_id')) {
                $table->foreignId('log_serah_terima_id')
                      ->nullable()
                      ->after('lokasi_baru')
                      ->constrained('log_serah_terima')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_assets', function (Blueprint $table) {
            if (Schema::hasColumn('mutasi_assets', 'log_serah_terima_id')) {
                $table->dropForeign(['log_serah_terima_id']);
                $table->dropColumn('log_serah_terima_id');
            }
            if (Schema::hasColumn('mutasi_assets', 'approved_by_id')) {
                $table->dropForeign(['approved_by_id']);
                $table->dropColumn('approved_by_id');
            }
            if (Schema::hasColumn('mutasi_assets', 'pemohon_id')) {
                $table->dropForeign(['pemohon_id']);
                $table->dropColumn('pemohon_id');
            }
            $table->dropColumn(['status', 'alasan_penolakan', 'lokasi_baru']);
        });
    }
};
