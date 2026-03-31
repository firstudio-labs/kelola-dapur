<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_distribusi_detail_dokumentasi')) {
            Schema::create('order_distribusi_detail_dokumentasi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_detail');
                $table->string('path_gambar');
                $table->timestamps();

                $table->foreign('id_detail')->references('id_detail')->on('order_distribusi_details')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_distribusi_detail_dokumentasi');
    }
};
