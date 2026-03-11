<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_produksi', function (Blueprint $table) {
            $table->id('id_order');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_dapur');
            $table->enum('status', ['belum_dibuat', 'sedang_dibuat', 'selesai'])->default('belum_dibuat');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi_dapur')->onDelete('cascade');
            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');

            $table->index(['id_dapur', 'status'], 'idx_order_produksi_dapur_status');
            $table->index(['id_transaksi'], 'idx_order_produksi_transaksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_produksi');
    }
};
