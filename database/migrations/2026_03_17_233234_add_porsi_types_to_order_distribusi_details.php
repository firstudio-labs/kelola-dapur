<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('order_distribusi_details', function (Blueprint $table) {
            $table->integer('porsi_besar')->default(0)->after('id_penerima');
            $table->integer('porsi_kecil')->default(0)->after('porsi_besar');
        });
    }

    public function down(): void
    {
        Schema::table('order_distribusi_details', function (Blueprint $table) {
            $table->dropColumn(['porsi_besar', 'porsi_kecil']);
        });
    }
};
