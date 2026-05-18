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
        Schema::table('accounting_transaction_shortages', function (Blueprint $table) {
            $table->decimal('harga_satuan', 15, 2)->after('laporan_kekurangan_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_transaction_shortages', function (Blueprint $table) {
            $table->dropColumn('harga_satuan');
        });
    }
};
