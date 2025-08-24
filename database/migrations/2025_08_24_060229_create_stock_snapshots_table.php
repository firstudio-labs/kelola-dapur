<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_snapshots', function (Blueprint $table) {
            $table->id('id_stock_snapshot');
            $table->unsignedBigInteger('id_approval_transaksi');
            $table->unsignedBigInteger('id_template_item');
            $table->decimal('available', 10, 2);
            $table->string('satuan');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_approval_transaksi')->references('id_approval_transaksi')->on('approval_transaksi')->onDelete('cascade');
            $table->foreign('id_template_item')->references('id_template_item')->on('template_items')->onDelete('cascade');

            // Indexes
            $table->index(['id_approval_transaksi'], 'idx_stock_snapshot_approval');
            $table->index(['id_template_item'], 'idx_stock_snapshot_template_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_snapshots');
    }
};
