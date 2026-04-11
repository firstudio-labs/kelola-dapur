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
        Schema::create('sarpas', function (Blueprint $table) {
            $table->id('id_sarpas');
            $table->unsignedBigInteger('id_user_role');
            $table->unsignedBigInteger('id_dapur');
            $table->string('nik_sarpas', 16)->nullable();
            $table->string('nama_lengkap')->nullable();
            
            // Address details
            $table->text('alamat_detail')->nullable();
            $table->string('province_code')->nullable();
            $table->string('province_name')->nullable();
            $table->string('regency_code')->nullable();
            $table->string('regency_name')->nullable();
            $table->string('district_code')->nullable();
            $table->string('district_name')->nullable();
            $table->string('village_code')->nullable();
            $table->string('village_name')->nullable();
            
            // Personal & Contact Info
            $table->string('kontak_wa', 20)->nullable();
            $table->string('pendidikan')->nullable(); // SD, SMP, SMA, D1, D2, D3, Sarjana
            $table->string('jenis_kelamin')->nullable(); // Pria, Wanita
            $table->string('foto_diri')->nullable();
            $table->string('jabatan')->default('Anggota'); // Penanggung jawab, Anggota
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_user_role')->references('id_user_role')->on('user_roles')->onDelete('cascade');
            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sarpas');
    }
};
