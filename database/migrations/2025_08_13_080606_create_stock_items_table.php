<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id('id_stock_item');
            $table->unsignedBigInteger('id_dapur');
            $table->unsignedBigInteger('id_template_item');
            $table->decimal('jumlah', 12, 3)->default(0);
            $table->date('tanggal_restok')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');
            $table->foreign('id_template_item')->references('id_template_item')->on('template_items')->onDelete('cascade');

            // Performance Indexes & Constraints
            $table->unique(['id_dapur', 'id_template_item'], 'uk_stock_items_dapur_template');
            $table->index(['id_dapur'], 'idx_stock_items_dapur');
            $table->index(['id_template_item'], 'idx_stock_items_template');
            $table->index(['tanggal_restok'], 'idx_stock_items_restok');
            $table->index(['jumlah'], 'idx_stock_items_jumlah');
            $table->index(['id_dapur', 'jumlah'], 'idx_stock_items_dapur_jumlah');
            $table->index(['tanggal_restok', 'id_dapur'], 'idx_stock_items_restok_dapur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
