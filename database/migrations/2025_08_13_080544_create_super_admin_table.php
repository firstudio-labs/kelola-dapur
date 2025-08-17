<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('super_admin', function (Blueprint $table) {
            $table->id('id_super_admin');
            $table->unsignedBigInteger('id_user_role');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_user_role')->references('id_user_role')->on('user_roles')->onDelete('cascade')->where('role_type', 'super_admin');

            // Performance Indexes
            $table->index(['id_user_role'], 'idx_super_admin_user_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('super_admin');
    }
};
