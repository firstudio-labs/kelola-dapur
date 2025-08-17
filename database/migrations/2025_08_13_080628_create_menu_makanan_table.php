<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_makanan', function (Blueprint $table) {
            $table->id('id_menu');
            $table->string('nama_menu', 100)->unique();
            $table->string('gambar_menu', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Performance Indexes
            $table->index(['nama_menu'], 'idx_menu_makanan_nama');
            $table->index(['is_active'], 'idx_menu_makanan_active');
            $table->index(['is_active', 'nama_menu'], 'idx_menu_makanan_active_nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_makanan');
    }
};
