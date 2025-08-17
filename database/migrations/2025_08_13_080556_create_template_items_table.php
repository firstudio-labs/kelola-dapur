<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_items', function (Blueprint $table) {
            $table->id('id_template_item');
            $table->string('nama_bahan', 100)->unique();
            $table->enum('satuan', ['kg', 'gram', 'liter', 'ml', 'pcs', 'pack', 'botol', 'kaleng', 'ikat', 'buah']);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Performance Indexes
            $table->index(['nama_bahan'], 'idx_template_items_nama');
            $table->index(['satuan'], 'idx_template_items_satuan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_items');
    }
};
