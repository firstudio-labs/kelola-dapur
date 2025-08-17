<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dapur', function (Blueprint $table) {
            $table->id('id_dapur');
            $table->string('nama_dapur', 100);
            $table->string('province_code', 2)->nullable();
            $table->string('province_name', 255)->nullable();
            $table->string('regency_code', 5)->nullable();
            $table->string('regency_name', 255)->nullable();
            $table->string('district_code', 8)->nullable();
            $table->string('district_name', 255)->nullable();
            $table->string('village_code', 11)->nullable();
            $table->string('village_name', 255)->nullable();
            $table->string('alamat', 500);
            $table->string('telepon', 14)->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->date('subscription_end')->nullable();
            $table->timestamps();

            // Performance Indexes
            $table->index(['status'], 'idx_dapur_status');
            $table->index(['province_code'], 'idx_dapur_province_code');
            $table->index(['regency_code'], 'idx_dapur_regency_code');
            $table->index(['district_code'], 'idx_dapur_district_code');
            $table->index(['village_code'], 'idx_dapur_village_code');
            $table->index(['subscription_end'], 'idx_dapur_subscription');
            $table->index(['status', 'province_code'], 'idx_dapur_status_province');
            $table->index(['nama_dapur'], 'idx_dapur_nama');
            $table->index(['created_at'], 'idx_dapur_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dapur');
    }
};
