<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_gudang', function (Blueprint $table) {
            $table->id('id_admin_gudang');
            $table->unsignedBigInteger('id_user_role');
            $table->unsignedBigInteger('id_dapur');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_user_role')->references('id_user_role')->on('user_roles')->onDelete('cascade')->where('role_type', 'admin_gudang');
            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');

            // Performance Indexes
            $table->index(['id_dapur'], 'idx_admin_gudang_dapur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_gudang');
    }
};
