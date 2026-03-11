<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('kategori_prasarana', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('item_prasarana', function (Blueprint $table) {
            $table->id('id_item');
            $table->unsignedBigInteger('id_kategori');
            $table->string('nama_item', 150);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_prasarana')->onDelete('cascade');
        });

        Schema::create('dapur_prasarana', function (Blueprint $table) {
            $table->id('id_dapur_prasarana');
            $table->unsignedBigInteger('id_dapur');
            $table->unsignedBigInteger('id_item');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');
            $table->foreign('id_item')->references('id_item')->on('item_prasarana')->onDelete('cascade');
            
            $table->unique(['id_dapur', 'id_item'], 'idx_unique_dapur_item');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('dapur_prasarana');
        Schema::dropIfExists('item_prasarana');
        Schema::dropIfExists('kategori_prasarana');
    }
};
