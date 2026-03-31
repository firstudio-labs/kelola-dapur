<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('order_produksi_dokumentasi', function (Blueprint $table) {
            $table->id('id_dokumentasi');
            $table->unsignedBigInteger('id_order');
            $table->string('path_gambar');
            $table->timestamps();

            $table->foreign('id_order')->references('id_order')->on('order_produksi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_produksi_dokumentasi');
    }
};
