<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('kepala_dapur', function (Blueprint $table) {
            $table->string('nik_kepala_sppg', 16)->nullable()->after('id_dapur');
            $table->text('alamat_detail')->nullable()->after('nik_kepala_sppg');
            $table->string('kode_provinsi', 2)->nullable()->after('alamat_detail');
            $table->string('kode_kabupaten', 5)->nullable()->after('kode_provinsi');
            $table->string('kode_kecamatan', 8)->nullable()->after('kode_kabupaten');
            $table->string('kode_desa', 11)->nullable()->after('kode_kecamatan');
            $table->string('kontak_wa', 15)->nullable()->after('kode_desa');
            $table->enum('pendidikan_terakhir', ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'Sarjana'])->nullable()->after('kontak_wa');
            $table->enum('jenis_kelamin', ['Pria', 'Wanita'])->nullable()->after('pendidikan_terakhir');
            $table->string('foto_diri')->nullable()->after('jenis_kelamin');
        });
    }

    public function down(): void
    {
        Schema::table('kepala_dapur', function (Blueprint $table) {
            $table->dropColumn([
                'nik_kepala_sppg',
                'alamat_detail',
                'kode_provinsi',
                'kode_kabupaten',
                'kode_kecamatan',
                'kode_desa',
                'kontak_wa',
                'pendidikan_terakhir',
                'jenis_kelamin',
                'foto_diri'
            ]);
        });
    }
};
