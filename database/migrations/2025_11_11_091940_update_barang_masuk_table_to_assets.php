<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            // 1. HAPUS KOLOM LAMA (YANG SALAH)
            $table->dropColumn(['no_sj', 'no_ppi', 'no_po', 'nama_barang', 'kategori', 'jumlah']);

            // 2. TAMBAH LINK KE DOKUMEN SJ
            $table->foreignId('surat_jalan_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('surat_jalan', 'id_sj')
                  ->onDelete('set null');

            // 3. TAMBAH LINK KE KATALOG (INI KUNCI "OTOMATIS KE ISI")
            $table->foreignId('master_barang_id')
                  ->nullable()
                  ->after('surat_jalan_id')
                  ->constrained('master_barang')
                  ->onDelete('set null');

            // 4. TAMBAH IDENTITAS FISIK (WAJIB)
            $table->string('serial_number')->unique()->after('master_barang_id');
            $table->string('kode_asset')->unique()->after('serial_number');

            // 5. TAMBAH INFO STATUS & PEMEGANG
            $table->string('status')->default('Stok')->after('kode_asset'); // (Stok, Dipakai, Rusak)
            $table->foreignId('user_pemegang_id')
                  ->nullable()
                  ->after('status')
                  ->constrained('users') // (Asumsi tabel user lo 'users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            // (Bikin kebalikannya)
            $table->string('no_sj')->nullable();
            $table->string('no_ppi')->nullable();
            $table->string('no_po')->nullable();
            $table->string('nama_barang')->nullable();
            $table->string('kategori')->nullable();
            $table->integer('jumlah')->nullable();

            $table->dropForeign(['surat_jalan_id']);
            $table->dropForeign(['master_barang_id']);
            $table->dropForeign(['user_pemegang_id']);
            $table->dropColumn([
                'surat_jalan_id', 
                'master_barang_id', 
                'serial_number', 
                'kode_asset', 
                'status', 
                'user_pemegang_id'
            ]);
        });
    }
};