<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_stock_items', function (Blueprint $table) {
            $table->id('id_approval_stock_item');
            $table->unsignedBigInteger('id_admin_gudang');
            $table->unsignedBigInteger('id_kepala_dapur');
            $table->unsignedBigInteger('id_stock_item');
            $table->decimal('jumlah', 12, 3);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_admin_gudang')->references('id_admin_gudang')->on('admin_gudang')->onDelete('cascade');
            $table->foreign('id_kepala_dapur')->references('id_kepala_dapur')->on('kepala_dapur')->onDelete('cascade');
            $table->foreign('id_stock_item')->references('id_stock_item')->on('stock_items')->onDelete('cascade');

            // Performance Indexes
            $table->index(['status'], 'idx_approval_stock_status');
            $table->index(['id_admin_gudang'], 'idx_approval_stock_admin');
            $table->index(['id_kepala_dapur'], 'idx_approval_stock_kepala');
            $table->index(['id_stock_item'], 'idx_approval_stock_item');
            $table->index(['status', 'created_at'], 'idx_approval_stock_status_created');
            $table->index(['approved_at'], 'idx_approval_stock_approved_at');
            $table->index(['id_admin_gudang', 'status'], 'idx_approval_stock_admin_status');
            $table->index(['id_kepala_dapur', 'status'], 'idx_approval_stock_kepala_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_stock_items');
    }
};
