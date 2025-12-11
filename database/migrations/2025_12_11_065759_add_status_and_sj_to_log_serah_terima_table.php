<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndSjToLogSerahTerimaTable extends Migration
{
    public function up()
    {
        Schema::table('log_serah_terima', function (Blueprint $table) {

            // STATUS
            $table->enum('status', [
                'draft',
                'menunggu_ttd_user',
                'menunggu_ttd_admin',
                'selesai'
            ])->default('draft')->after('id'); 

            // ID SURAT JALAN
            $table->unsignedBigInteger('id_surat_jalan')->nullable()->after('status');

        });
    }

    public function down()
    {
        Schema::table('log_serah_terima', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('id_surat_jalan');
        });
    }
}
