<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accounting_categories', function (Blueprint $table) {
            $table->boolean('is_protected')->default(false)->after('is_tax');
        });

        // Mark the "Pembelian Bahan Baku" categories (both expense & income) as protected
        DB::table('accounting_categories')
            ->whereIn('group', ['biaya_bahan_baku', 'dana_bahan_baku'])
            ->where('name', 'Pembelian Bahan Baku')
            ->update(['is_protected' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_categories', function (Blueprint $table) {
            $table->dropColumn('is_protected');
        });
    }
};

