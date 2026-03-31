<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->decimal('konversi_nilai', 10, 3)->nullable()->after('jumlah');
            $table->string('konversi_satuan', 50)->nullable()->after('konversi_nilai');
        });
    }

    public function down(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropColumn(['konversi_nilai', 'konversi_satuan']);
        });
    }
};
