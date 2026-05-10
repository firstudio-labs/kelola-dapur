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
        Schema::create('mitra_dokumentasi_fotos', function (Blueprint $table) {
            $table->id('id_foto');
            $table->unsignedBigInteger('id_dokumentasi');
            $table->string('url');
            
            $table->foreign('id_dokumentasi')->references('id_dokumentasi')->on('mitra_dokumentasis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra_dokumentasi_fotos');
    }
};
