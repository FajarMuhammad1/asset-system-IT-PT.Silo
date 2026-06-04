<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('mutasi_assets', function (Blueprint $table) {
        // Menghapus kolom lama yang salah (jika ada)
        if (Schema::hasColumn('mutasi_assets', 'id_barang_masuk')) {
            $table->dropColumn('id_barang_masuk');
        }

        // Menambahkan kolom baru yang benar setelah kolom id
        $table->foreignId('barang_masuk_id')->after('id')->constrained('barang_masuk')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('mutasi_assets', function (Blueprint $table) {
        $table->dropForeign(['barang_masuk_id']);
        $table->dropColumn('barang_masuk_id');
    });
}
};
