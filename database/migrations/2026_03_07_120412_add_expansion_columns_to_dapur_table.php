<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('dapur', function (Blueprint $table) {
            $table->string('no_registrasi_sppg', 100)->nullable()->after('nama_dapur');
            $table->string('nik_pemilik', 16)->nullable()->after('no_registrasi_sppg');
            $table->string('foto_bangunan')->nullable()->after('alamat');
            $table->text('tag_lokasi')->nullable()->after('foto_bangunan');
        });
    }

    public function down(): void
    {
        Schema::table('dapur', function (Blueprint $table) {
            $table->dropColumn([
                'no_registrasi_sppg',
                'nik_pemilik',
                'foto_bangunan',
                'tag_lokasi'
            ]);
        });
    }
};
