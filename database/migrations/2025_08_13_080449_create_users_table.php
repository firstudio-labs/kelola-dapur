<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama', 100);
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Performance Indexes
            $table->index(['is_active'], 'idx_users_active');
            $table->index(['nama'], 'idx_users_nama');
            $table->index(['is_active', 'created_at'], 'idx_users_active_created');
        });

        // Tabel User Roles
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id('id_user_role');
            $table->unsignedBigInteger('id_user')->unique(); // Unik untuk memastikan satu role per user
            $table->enum('role_type', ['kepala_dapur', 'ahli_gizi', 'admin_gudang', 'super_admin']);
            $table->unsignedBigInteger('id_dapur')->nullable(); // Nullable untuk super_admin
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');

            // Performance Indexes
            $table->index(['role_type'], 'idx_user_roles_type');
            $table->index(['id_dapur'], 'idx_user_roles_dapur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_roles');
    }
};
