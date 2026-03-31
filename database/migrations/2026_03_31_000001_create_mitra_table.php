<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitra', function (Blueprint $table) {
            $table->id('id_mitra');
            $table->unsignedBigInteger('id_user_role')->unique();
            $table->string('nik_pemilik', 20)->nullable();
            $table->string('nama_pemilik', 255);
            $table->string('province_code', 10)->nullable();
            $table->string('province_name', 100)->nullable();
            $table->string('regency_code', 10)->nullable();
            $table->string('regency_name', 100)->nullable();
            $table->string('district_code', 10)->nullable();
            $table->string('district_name', 100)->nullable();
            $table->string('village_code', 15)->nullable();
            $table->string('village_name', 100)->nullable();
            $table->text('alamat_detail')->nullable();
            $table->timestamps();

            $table->foreign('id_user_role')->references('id_user_role')->on('user_roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra');
    }
};
