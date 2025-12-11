<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('log_serah_terima', function (Blueprint $table) {

            // 1. STATUS WORKFLOW
            if (!Schema::hasColumn('log_serah_terima', 'status')) {
                $table->enum('status', [
                    'draft',
                    'menunggu_ttd_user',
                    'menunggu_ttd_admin',
                    'selesai'
                ])->default('draft')->after('updated_at');
            }

            // 2. RELASI ASET
            if (!Schema::hasColumn('log_serah_terima', 'barang_masuk_id')) {
                $table->unsignedBigInteger('barang_masuk_id')->nullable()->after('id_surat_jalan');
                $table->foreign('barang_masuk_id')
                    ->references('id')->on('barang_masuk')
                    ->nullOnDelete();
            }

            // 3. USER PENERIMA
            if (!Schema::hasColumn('log_serah_terima', 'user_pemegang_id')) {
                $table->unsignedBigInteger('user_pemegang_id')->nullable()->after('barang_masuk_id');
                $table->foreign('user_pemegang_id')
                    ->references('id')->on('users')
                    ->nullOnDelete();
            }

            // 4. ADMIN PEMBUAT
            if (!Schema::hasColumn('log_serah_terima', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('user_pemegang_id');
                $table->foreign('created_by')
                    ->references('id')->on('users')
                    ->nullOnDelete();
            }

            // 5. TTD PENERIMA
            if (!Schema::hasColumn('log_serah_terima', 'ttd_penerima')) {
                $table->longText('ttd_penerima')->nullable()->after('created_by');
            }

            // 6. Rename ttd_serah → ttd_petugas
            if (Schema::hasColumn('log_serah_terima', 'ttd_serah')
                && !Schema::hasColumn('log_serah_terima', 'ttd_petugas')) {

                $table->renameColumn('ttd_serah', 'ttd_petugas');
            }

            // 6B. Jika ttd_petugas memang belum ada sama sekali
            if (!Schema::hasColumn('log_serah_terima', 'ttd_petugas')) {
                $table->longText('ttd_petugas')->nullable()->after('ttd_penerima');
            }

            // 7. FILE PDF FINAL
            if (!Schema::hasColumn('log_serah_terima', 'file_bast')) {
                $table->string('file_bast')->nullable()->after('ttd_petugas');
            }

            // 8. TANGGAL SERAH TERIMA
            if (!Schema::hasColumn('log_serah_terima', 'tanggal_serah_terima')) {
                $table->date('tanggal_serah_terima')->nullable()->after('file_bast');
            }

            // 9. FOTO BUKTI (jangan dihapus)
            if (!Schema::hasColumn('log_serah_terima', 'foto_bukti')) {
                $table->string('foto_bukti')->nullable()->after('tanggal_serah_terima');
            }
        });
    }

    public function down()
    {
        Schema::table('log_serah_terima', function (Blueprint $table) {

            // Drop FK agar tidak error rollback
            $fks = ['barang_masuk_id', 'user_pemegang_id', 'created_by'];

            foreach ($fks as $fk) {
                $fkName = 'log_serah_terima_'.$fk.'_foreign';
                if (Schema::hasColumn('log_serah_terima', $fk)) {
                    $table->dropForeign($fkName);
                }
            }

            // Kolom yang dihapus saat rollback
            $columns = [
                'status',
                'barang_masuk_id',
                'user_pemegang_id',
                'created_by',
                'ttd_penerima',
                'ttd_petugas',
                'file_bast',
                'tanggal_serah_terima',
                // foto_bukti TIDAK DIHAPUS
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('log_serah_terima', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
