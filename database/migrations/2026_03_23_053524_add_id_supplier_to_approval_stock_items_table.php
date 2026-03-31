<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('approval_stock_items', function (Blueprint $table) {
            $table->unsignedBigInteger('id_supplier')->nullable()->after('id_stock_item');
            $table->foreign('id_supplier')->references('id_supplier')->on('suppliers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('approval_stock_items', function (Blueprint $table) {
            $table->dropForeign(['id_supplier']);
            $table->dropColumn('id_supplier');
        });
    }
};
