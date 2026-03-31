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
        Schema::create('approval_stock_item_suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_approval_stock_item');
            $table->unsignedBigInteger('id_supplier');
            $table->decimal('jumlah', 15, 3);
            $table->timestamps();

            $table->foreign('id_approval_stock_item', 'fk_asis_approval')
                ->references('id_approval_stock_item')
                ->on('approval_stock_items')
                ->onDelete('cascade');
            $table->foreign('id_supplier', 'fk_asis_supplier')
                ->references('id_supplier')
                ->on('suppliers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_stock_item_suppliers');
    }
};
