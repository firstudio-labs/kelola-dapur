<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('kepala_dapur', function (Blueprint $table) {
            $table->dropColumn(['kode_provinsi', 'kode_kabupaten', 'kode_kecamatan', 'kode_desa']);

            $table->string('province_code', 2)->nullable()->after('alamat_detail');
            $table->string('province_name')->nullable()->after('province_code');

            $table->string('regency_code', 4)->nullable()->after('province_name');
            $table->string('regency_name')->nullable()->after('regency_code');

            $table->string('district_code', 7)->nullable()->after('regency_name');
            $table->string('district_name')->nullable()->after('district_code');

            $table->string('village_code', 10)->nullable()->after('district_name');
            $table->string('village_name')->nullable()->after('village_code');
        });
    }

    public function down(): void
    {
        Schema::table('kepala_dapur', function (Blueprint $table) {
            $table->dropColumn([
                'province_code',
                'province_name',
                'regency_code',
                'regency_name',
                'district_code',
                'district_name',
                'village_code',
                'village_name'
            ]);

            $table->string('kode_provinsi', 2)->nullable()->after('alamat_detail');
            $table->string('kode_kabupaten', 5)->nullable()->after('kode_provinsi');
            $table->string('kode_kecamatan', 8)->nullable()->after('kode_kabupaten');
            $table->string('kode_desa', 11)->nullable()->after('kode_kecamatan');
        });
    }
};
