<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('id_supplier');
            $table->unsignedBigInteger('id_dapur');
            $table->string('nama_supplier');
            $table->string('kontak')->nullable();
            $table->text('alamat')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
