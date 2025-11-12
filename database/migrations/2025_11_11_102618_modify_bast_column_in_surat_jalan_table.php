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
        Schema::table('surat_jalan', function (Blueprint $table) {
            // 1. HAPUS kolom 'bast' yang lama (tipe string/text)
            $table->dropColumn('bast');
            
            // 2. TAMBAH kolom baru (tipe boolean), kita kasih nama lebih jelas
            $table->boolean('is_bast_submitted')->default(false)->after('jenis_surat_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan', function (Blueprint $table) {
            // 1. HAPUS kolom boolean
            $table->dropColumn('is_bast_submitted');

            // 2. BALIKIN kolom string lama
            $table->string('bast')->nullable()->after('jenis_surat_jalan');
        });
    }
};