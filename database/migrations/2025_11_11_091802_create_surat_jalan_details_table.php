<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_jalan_details', function (Blueprint $table) {
            $table->id();

            // Link ke Header SJ (Pake 'id_sj' Primary Key lo)
            $table->foreignId('surat_jalan_id')
                  ->constrained('surat_jalan', 'id_sj') // (Tabel target, Kolom PK target)
                  ->onDelete('cascade'); // (Kalo SJ dihapus, detailnya ikut kehapus)

            // Link ke Katalog (Kamus)
            $table->foreignId('master_barang_id')
                  ->constrained('master_barang')
                  ->onDelete('restrict'); // (Gaboleh hapus master kalo masih dipake)

            $table->integer('qty'); // Jumlah barang (Misal: 5 Laptop)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_details');
    }
};