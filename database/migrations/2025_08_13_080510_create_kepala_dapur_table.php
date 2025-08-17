<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kepala_dapur', function (Blueprint $table) {
            $table->id('id_kepala_dapur');
            $table->unsignedBigInteger('id_user_role');
            $table->unsignedBigInteger('id_dapur');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_user_role')->references('id_user_role')->on('user_roles')->onDelete('cascade')->where('role_type', 'kepala_dapur');
            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');

            // Performance Indexes
            $table->index(['id_dapur'], 'idx_kepala_dapur_dapur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kepala_dapur');
    }
};
