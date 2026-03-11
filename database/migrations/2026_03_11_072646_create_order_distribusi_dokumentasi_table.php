<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_distribusi_dokumentasi', function (Blueprint $table) {
            $table->id('id_dokumentasi_distribusi');
            $table->unsignedBigInteger('id_distribusi');
            $table->string('path_gambar');
            $table->timestamps();

            $table->foreign('id_distribusi')->references('id_distribusi')->on('order_distribusi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_distribusi_dokumentasi');
    }
};
