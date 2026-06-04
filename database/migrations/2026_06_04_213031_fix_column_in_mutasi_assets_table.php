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
            // 1. Cek jika di database ternyata ada kolom lama yang salah (id_barang_masuk)
            if (Schema::hasColumn('mutasi_assets', 'id_barang_masuk')) {
                // Hapus foreign key-nya terlebih dahulu jika ada agar tidak error constraint
                try {
                    $table->dropForeign(['id_barang_masuk']);
                } catch (\Exception $e) {
                    // Abaikan jika ternyata tidak ada relasi constraint-nya
                }
                
                // Hapus kolom yang salah tersebut
                $table->dropColumn('id_barang_masuk');
            }

            // 2. Tambahkan kolom baru yang BENAR sesuai standar Laravel (barang_masuk_id)
            if (!Schema::hasColumn('mutasi_assets', 'barang_masuk_id')) {
                $table->foreignId('barang_masuk_id')
                      ->after('id') // diletakkan setelah kolom id
                      ->constrained('barang_masuk') // otomatis terhubung ke id di tabel barang_masuk
                      ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_assets', function (Blueprint $table) {
            // Rollback: Hapus foreign key dan kolom jika migration di-rollback
            if (Schema::hasColumn('mutasi_assets', 'barang_masuk_id')) {
                $table->dropForeign(['barang_masuk_id']);
                $table->dropColumn('barang_masuk_id');
            }
        });
    }
};