<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('ahli_gizi', function (Blueprint $table) {
            $table->string('nik_ahli_gizi', 16)->nullable()->after('id_dapur');
            $table->enum('jabatan', ['Penanggung jawab', 'Anggota'])->nullable()->after('nik_ahli_gizi');
            $table->text('alamat_detail')->nullable()->after('jabatan');
            $table->string('province_code', 2)->nullable()->after('alamat_detail');
            $table->string('province_name')->nullable()->after('province_code');
            $table->string('regency_code', 4)->nullable()->after('province_name');
            $table->string('regency_name')->nullable()->after('regency_code');
            $table->string('district_code', 7)->nullable()->after('regency_name');
            $table->string('district_name')->nullable()->after('district_code');
            $table->string('village_code', 10)->nullable()->after('district_name');
            $table->string('village_name')->nullable()->after('village_code');
            $table->string('kontak_wa', 15)->nullable()->after('village_name');
            $table->enum('pendidikan_terakhir', ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'Sarjana'])->nullable()->after('kontak_wa');
            $table->enum('jenis_kelamin', ['Pria', 'Wanita'])->nullable()->after('pendidikan_terakhir');
            $table->string('foto_diri')->nullable()->after('jenis_kelamin');
        });
    }

    public function down(): void
    {
        Schema::table('ahli_gizi', function (Blueprint $table) {
            $table->dropColumn([
                'nik_ahli_gizi',
                'jabatan',
                'alamat_detail',
                'province_code',
                'province_name',
                'regency_code',
                'regency_name',
                'district_code',
                'district_name',
                'village_code',
                'village_name',
                'kontak_wa',
                'pendidikan_terakhir',
                'jenis_kelamin',
                'foto_diri'
            ]);
        });
    }
};
