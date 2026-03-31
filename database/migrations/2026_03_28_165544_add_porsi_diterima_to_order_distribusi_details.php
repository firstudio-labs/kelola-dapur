<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_distribusi_details', function (Blueprint $table) {
            $table->integer('porsi_besar_diterima')->nullable()->after('ulasan');
            $table->integer('porsi_kecil_diterima')->nullable()->after('porsi_besar_diterima');
        });
    }

    public function down(): void
    {
        Schema::table('order_distribusi_details', function (Blueprint $table) {
            $table->dropColumn(['porsi_besar_diterima', 'porsi_kecil_diterima']);
        });
    }
};
