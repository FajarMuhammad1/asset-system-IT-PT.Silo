<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang'); // Misal: "Laptop Latitude 5440"
            $table->string('kategori');    // Misal: "Laptop"
            $table->string('merk');       // Misal: "Dell"
            $table->text('spesifikasi')->nullable(); // Misal: "i5, 16GB RAM, 512GB SSD"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_barang');
    }
};