<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('approval_stock_item_dokumentasis', function (Blueprint $table) {
            $table->unsignedBigInteger('id_approval_stock_item_supplier')->nullable()->after('id_approval_stock_item');
            $table->foreign('id_approval_stock_item_supplier', 'fk_asid_asis')
                ->references('id')
                ->on('approval_stock_item_suppliers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_stock_item_dokumentasis', function (Blueprint $table) {
            $table->dropForeign('fk_asid_asis');
            $table->dropColumn('id_approval_stock_item_supplier');
        });
    }
};
