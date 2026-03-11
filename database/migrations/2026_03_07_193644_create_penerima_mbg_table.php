<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerima_mbg', function (Blueprint $table) {
            $table->bigIncrements('id_penerima');
            $table->unsignedBigInteger('id_user_role');
            $table->unsignedBigInteger('id_dapur')->nullable();
            $table->enum('id_type', ['nik', 'nisn', 'no_registrasi'])->default('nik');
            $table->string('id_number', 50)->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('province_code', 10)->nullable();
            $table->string('province_name', 100)->nullable();
            $table->string('regency_code', 10)->nullable();
            $table->string('regency_name', 100)->nullable();
            $table->string('district_code', 10)->nullable();
            $table->string('district_name', 100)->nullable();
            $table->string('village_code', 10)->nullable();
            $table->string('village_name', 100)->nullable();
            $table->text('alamat_detail')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('link_gmaps')->nullable();
            $table->string('foto_lokasi')->nullable();
            $table->integer('jumlah_porsi')->default(1);
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_approval')->nullable();
            $table->timestamps();

            $table->foreign('id_user_role')->references('id_user_role')->on('user_roles')->onDelete('cascade');
            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerima_mbg');
    }
};
