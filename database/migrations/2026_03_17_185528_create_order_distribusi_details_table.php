<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_distribusi_details', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_distribusi');
            $table->unsignedBigInteger('id_penerima');
            $table->integer('jumlah_diterima')->default(0);
            $table->enum('status', ['belum_dikirim', 'sedang_dikirim', 'sudah_dikirim'])->default('belum_dikirim');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_distribusi')->references('id_distribusi')->on('order_distribusi')->onDelete('cascade');
            $table->foreign('id_penerima')->references('id_penerima')->on('penerima_mbg')->onDelete('cascade');

            $table->unique(['id_distribusi', 'id_penerima']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_distribusi_details');
    }
};
