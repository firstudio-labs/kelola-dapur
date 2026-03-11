<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('produksi', function (Blueprint $table) {
            $table->id('id_produksi');
            $table->unsignedBigInteger('id_user_role');
            $table->unsignedBigInteger('id_dapur');
            
            $table->string('nik_produksi', 16)->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('kontak_wa', 20)->nullable();
            $table->enum('pendidikan', ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'Sarjana'])->nullable();
            $table->enum('jenis_kelamin', ['Pria', 'Wanita'])->nullable();
            $table->string('foto_diri')->nullable();
            $table->enum('jabatan', ['Penanggung jawab', 'Anggota'])->default('Anggota');
            
            $table->string('province_code')->nullable();
            $table->string('province_name')->nullable();
            $table->string('regency_code')->nullable();
            $table->string('regency_name')->nullable();
            $table->string('district_code')->nullable();
            $table->string('district_name')->nullable();
            $table->string('village_code')->nullable();
            $table->string('village_name')->nullable();
            $table->text('alamat_detail')->nullable();
            
            $table->timestamps();

            $table->foreign('id_user_role')->references('id_user_role')->on('user_roles')->onDelete('cascade');
            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('produksi');
    }
};
