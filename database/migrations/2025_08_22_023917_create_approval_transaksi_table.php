<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_transaksi', function (Blueprint $table) {
            $table->id('id_approval_transaksi');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_ahli_gizi');
            $table->unsignedBigInteger('id_kepala_dapur');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi_dapur')->onDelete('cascade');
            $table->foreign('id_ahli_gizi')->references('id_ahli_gizi')->on('ahli_gizi')->onDelete('cascade');
            $table->foreign('id_kepala_dapur')->references('id_kepala_dapur')->on('kepala_dapur')->onDelete('cascade');

            // Indexes
            $table->index(['status'], 'idx_approval_transaksi_status');
            $table->index(['id_transaksi'], 'idx_approval_transaksi_transaksi');
            $table->index(['id_kepala_dapur', 'status'], 'idx_approval_transaksi_kepala_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_transaksi');
    }
};
