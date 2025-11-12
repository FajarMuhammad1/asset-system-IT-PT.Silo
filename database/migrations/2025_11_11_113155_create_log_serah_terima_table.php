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
    Schema::create('log_serah_terima', function (Blueprint $table) {
        $table->id();

        // Aset mana yang diserahin?
        $table->foreignId('barang_masuk_id')
              ->constrained('barang_masuk')
              ->onDelete('cascade'); // Kalo aset dihapus, log-nya ikut kehapus

        // Dikasih ke user mana?
        $table->foreignId('user_pemegang_id')
              ->constrained('users')
              ->onDelete('cascade'); // Kalo user dihapus

        // Siapa Admin/Staff yang nyerahin? (Buat audit)
        $table->foreignId('admin_id')
              ->constrained('users');

        $table->date('tanggal_serah_terima');
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('log_serah_terima');
}
};
