<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_menu', function (Blueprint $table) {
            $table->id('id_bahan_menu');
            $table->unsignedBigInteger('id_menu');
            $table->unsignedBigInteger('id_template_item');
            $table->decimal('jumlah_per_porsi', 10, 4);
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_menu')->references('id_menu')->on('menu_makanan')->onDelete('cascade');
            $table->foreign('id_template_item')->references('id_template_item')->on('template_items')->onDelete('cascade');

            // Performance Indexes & Constraints
            $table->unique(['id_menu', 'id_template_item'], 'uk_bahan_menu_menu_template');
            $table->index(['id_menu'], 'idx_bahan_menu_menu');
            $table->index(['id_template_item'], 'idx_bahan_menu_template');
            $table->index(['jumlah_per_porsi'], 'idx_bahan_menu_jumlah');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_menu');
    }
};
