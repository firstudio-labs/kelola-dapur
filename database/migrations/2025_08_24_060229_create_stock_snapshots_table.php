<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_snapshots', function (Blueprint $table) {
            $table->id('id_stock_snapshot');
            $table->unsignedBigInteger('id_approval_transaksi');
            $table->unsignedBigInteger('id_template_item');
            $table->decimal('available', 10, 3)->default(0);
            $table->string('satuan', 50);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_approval_transaksi')
                ->references('id_approval_transaksi')
                ->on('approval_transaksi')
                ->onDelete('cascade');

            $table->foreign('id_template_item')
                ->references('id_template_item')
                ->on('template_items')
                ->onDelete('cascade');

            // Indexes 
            $table->index(['id_approval_transaksi', 'id_template_item']);
            $table->index('id_approval_transaksi');
            $table->index('id_template_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_snapshots');
    }
};
