<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_transaksi_dapur', function (Blueprint $table) {
            $table->id('id_detail_transaksi');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_menu');
            $table->unsignedInteger('jumlah_porsi');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi_dapur')->onDelete('cascade');
            $table->foreign('id_menu')->references('id_menu')->on('menu_makanan')->onDelete('cascade');

            // Performance Indexes & Constraints
            $table->unique(['id_transaksi', 'id_menu'], 'uk_detail_transaksi_transaksi_menu');
            $table->index(['id_transaksi'], 'idx_detail_transaksi_transaksi');
            $table->index(['id_menu'], 'idx_detail_transaksi_menu');
            $table->index(['jumlah_porsi'], 'idx_detail_transaksi_jumlah');

            // Composite index for reporting
            $table->index(['id_menu', 'jumlah_porsi'], 'idx_detail_transaksi_menu_jumlah');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi_dapur');
    }
};
