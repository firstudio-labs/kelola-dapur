<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('template_items', function (Blueprint $table) {
            $table->json('kandungan_gizi')->nullable()->after('keterangan');
            $table->json('jenis_bahan')->nullable()->after('kandungan_gizi');
        });
    }

    
    public function down(): void
    {
        Schema::table('template_items', function (Blueprint $table) {
            
        });
    }
};
