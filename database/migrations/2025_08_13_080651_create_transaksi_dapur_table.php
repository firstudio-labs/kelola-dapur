<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_dapur', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->unsignedBigInteger('id_dapur');
            $table->datetime('tanggal_transaksi')->default(now());
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'processing', 'completed', 'cancelled'])->default('draft');
            $table->decimal('total_porsi', 10, 0)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');
            $table->foreign('created_by')->references('id_user')->on('users')->onDelete('set null');

            // Performance Indexes
            $table->index(['id_dapur'], 'idx_transaksi_dapur');
            $table->index(['status'], 'idx_transaksi_status');
            $table->index(['tanggal_transaksi'], 'idx_transaksi_tanggal');
            $table->index(['created_by'], 'idx_transaksi_created_by');

            // Composite indexes for common queries
            $table->index(['id_dapur', 'status'], 'idx_transaksi_dapur_status');
            $table->index(['id_dapur', 'tanggal_transaksi'], 'idx_transaksi_dapur_tanggal');
            $table->index(['status', 'tanggal_transaksi'], 'idx_transaksi_status_tanggal');
            $table->index(['id_dapur', 'status', 'tanggal_transaksi'], 'idx_transaksi_dapur_status_tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_dapur');
    }
};
