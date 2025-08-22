<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kekurangan_stock', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_template_item');
            $table->decimal('jumlah_dibutuhkan', 12, 3);
            $table->decimal('jumlah_tersedia', 12, 3);
            $table->decimal('jumlah_kurang', 12, 3);
            $table->enum('satuan', ['kg', 'gram', 'liter', 'ml', 'pcs', 'pack', 'botol', 'kaleng', 'ikat', 'buah']);
            $table->enum('status', ['pending', 'resolved'])->default('pending');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi_dapur')->onDelete('cascade');
            $table->foreign('id_template_item')->references('id_template_item')->on('template_items')->onDelete('cascade');

            // Indexes
            $table->index(['id_transaksi'], 'idx_laporan_transaksi');
            $table->index(['status'], 'idx_laporan_status');
            $table->index(['id_template_item'], 'idx_laporan_template');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kekurangan_stock');
    }
};
