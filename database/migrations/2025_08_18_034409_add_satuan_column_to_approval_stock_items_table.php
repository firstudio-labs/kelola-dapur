<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('approval_stock_items', function (Blueprint $table) {
            $table->enum('satuan', ['kg', 'gram', 'liter', 'ml', 'pcs', 'pack', 'botol', 'kaleng', 'ikat', 'buah'])
                ->after('jumlah')->nullable(false);
            $table->index('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('approval_stock_items', function (Blueprint $table) {
            //
        });
    }
};
