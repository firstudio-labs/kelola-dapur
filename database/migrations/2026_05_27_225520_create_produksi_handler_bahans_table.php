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
        Schema::create('produksi_handler_bahan', function (Blueprint $table) {
            $table->id('id_handler');
            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger('id_template_item');
            $table->enum('jenis', ['kelebihan', 'kekurangan']);
            $table->decimal('jumlah', 10, 2);
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'resolved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('id_order')->references('id_order')->on('order_produksi')->onDelete('cascade');
            $table->foreign('id_template_item')->references('id_template_item')->on('template_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksi_handler_bahan');
    }
};
