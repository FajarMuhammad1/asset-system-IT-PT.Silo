<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
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
            $table->foreign('barang_masuk_id')->references('id')->on('barang_masuk')->onDelete('set null');
        }

        // 3. USER PENERIMA (FK)
        if (!Schema::hasColumn('log_serah_terima', 'user_pemegang_id')) {
            $table->unsignedBigInteger('user_pemegang_id')->nullable()->after('barang_masuk_id');
            $table->foreign('user_pemegang_id')->references('id')->on('users')->onDelete('set null');
        }

        // 4. ADMIN PEMBUAT (FK)
        if (!Schema::hasColumn('log_serah_terima', 'created_by')) {
            $table->unsignedBigInteger('created_by')->nullable()->after('user_pemegang_id');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        }

        // 5. TTD PETUGAS (rename ttd_serah)
        if (Schema::hasColumn('log_serah_terima', 'ttd_serah')) {
            $table->renameColumn('ttd_serah', 'ttd_petugas');
        }

        // 6. FILE PDF FINAL
        if (!Schema::hasColumn('log_serah_terima', 'file_bast')) {
            $table->string('file_bast')->nullable()->after('ttd_petugas');
        }

        // 7. TANGGAL SERAH TERIMA
        if (!Schema::hasColumn('log_serah_terima', 'tanggal_serah_terima')) {
            $table->date('tanggal_serah_terima')->nullable()->after('file_bast');
        }
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
