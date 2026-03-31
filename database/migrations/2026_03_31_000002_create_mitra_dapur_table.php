<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitra_dapur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mitra');
            $table->unsignedBigInteger('id_dapur');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('id_mitra')->references('id_mitra')->on('mitra')->onDelete('cascade');
            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');

            // Satu mitra hanya bisa punya satu binding per dapur
            $table->unique(['id_mitra', 'id_dapur']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra_dapur');
    }
};
