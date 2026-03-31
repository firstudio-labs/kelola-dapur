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
        Schema::table('admin_gudang', function (Blueprint $table) {
            $table->string('nik_admin_gudang', 16)->nullable()->after('id_dapur');
            $table->string('nama_lengkap')->nullable()->after('nik_admin_gudang');
            $table->string('kontak_wa', 20)->nullable()->after('nama_lengkap');
            $table->enum('jabatan', ['Penanggung jawab', 'Anggota'])->default('Anggota')->after('kontak_wa');
            $table->string('pendidikan_terakhir')->nullable()->after('jabatan');
            $table->enum('jenis_kelamin', ['Pria', 'Wanita'])->nullable()->after('pendidikan_terakhir');
            $table->string('foto_diri')->nullable()->after('jenis_kelamin');
            $table->string('province_code')->nullable()->after('foto_diri');
            $table->string('province_name')->nullable()->after('province_code');
            $table->string('regency_code')->nullable()->after('province_name');
            $table->string('regency_name')->nullable()->after('regency_code');
            $table->string('district_code')->nullable()->after('regency_name');
            $table->string('district_name')->nullable()->after('district_code');
            $table->string('village_code')->nullable()->after('district_name');
            $table->string('village_name')->nullable()->after('village_code');
            $table->text('alamat_detail')->nullable()->after('village_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_gudang', function (Blueprint $table) {
            $table->dropColumn([
                'nik_admin_gudang',
                'nama_lengkap',
                'kontak_wa',
                'jabatan',
                'pendidikan_terakhir',
                'jenis_kelamin',
                'foto_diri',
                'province_code',
                'province_name',
                'regency_code',
                'regency_name',
                'district_code',
                'district_name',
                'village_code',
                'village_name',
                'alamat_detail',
            ]);
        });
    }
};
