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
            // 1. Hapus kolom lama 'id_barang_masuk' jika masih ada di database Anda
            if (Schema::hasColumn('mutasi_assets', 'id_barang_masuk')) {
                try { 
                    $table->dropForeign(['id_barang_masuk']); 
                } catch (\Exception $e) {
                    // Abaikan jika tidak ada foreign key constraint
                }
                $table->dropColumn('id_barang_masuk');
            }

            // 2. Pastikan kolom 'barang_masuk_id' ada
            if (!Schema::hasColumn('mutasi_assets', 'barang_masuk_id')) {
                $table->foreignId('barang_masuk_id')
                      ->after('id')
                      ->constrained('barang_masuk')
                      ->onDelete('cascade');
            }

            // 3. FIX: Pastikan kolom 'user_asal_id' dibuat (Bisa NULL jika awalnya barang berupa Stok gudang)
            if (!Schema::hasColumn('mutasi_assets', 'user_asal_id')) {
                $table->foreignId('user_asal_id')
                      ->nullable()
                      ->after('barang_masuk_id')
                      ->constrained('users')
                      ->onDelete('cascade');
            }

            // 4. Pastikan kolom 'user_tujuan_id' ada
            if (!Schema::hasColumn('mutasi_assets', 'user_tujuan_id')) {
                $table->foreignId('user_tujuan_id')
                      ->after('user_asal_id')
                      ->constrained('users')
                      ->onDelete('cascade');
            }

            // 5. Pastikan kolom 'keterangan' ada
            if (!Schema::hasColumn('mutasi_assets', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('user_tujuan_id');
            }

            // 6. Pastikan kolom 'tanggal_mutasi' ada
            if (!Schema::hasColumn('mutasi_assets', 'tanggal_mutasi')) {
                $table->dateTime('tanggal_mutasi')->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_assets', function (Blueprint $table) {
            // Rollback semua kolom yang ditambahkan jika bermasalah
            $columns = ['barang_masuk_id', 'user_asal_id', 'user_tujuan_id', 'keterangan', 'tanggal_mutasi'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('mutasi_assets', $column)) {
                    try { 
                        $table->dropForeign([$column]); 
                    } catch (\Exception $e) {}
                    $table->dropColumn($column);
                }
            }
        });
    }
};