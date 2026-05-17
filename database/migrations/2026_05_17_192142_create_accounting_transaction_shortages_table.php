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
        Schema::create('accounting_transaction_shortages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accounting_transaction_id');
            $table->unsignedBigInteger('laporan_kekurangan_id');
            $table->decimal('qty_dibeli', 10, 3);
            $table->decimal('nominal', 15, 2);
            $table->timestamps();

            $table->foreign('accounting_transaction_id', 'fk_ats_transaction')
                  ->references('id')
                  ->on('accounting_transactions')
                  ->onDelete('cascade');
                  
            $table->foreign('laporan_kekurangan_id', 'fk_ats_laporan')
                  ->references('id_laporan')
                  ->on('laporan_kekurangan_stock')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_transaction_shortages');
    }
};

