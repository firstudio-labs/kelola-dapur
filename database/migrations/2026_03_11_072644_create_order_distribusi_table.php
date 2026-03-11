<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_distribusi', function (Blueprint $table) {
            $table->id('id_distribusi');
            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger('id_dapur');
            $table->enum('status', ['belum_dikirim', 'sedang_dikirim', 'sudah_dikirim'])->default('belum_dikirim');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_order')->references('id_order')->on('order_produksi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_distribusi');
    }
};
