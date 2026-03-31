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
        Schema::create('approval_stock_item_dokumentasis', function (Blueprint $table) {
            $table->id('id_dokumentasi');
            $table->unsignedBigInteger('id_approval_stock_item');
            $table->string('foto_path');
            $table->timestamps();

            $table->foreign('id_approval_stock_item', 'fk_app_stk_itm_doc')
                  ->references('id_approval_stock_item')
                  ->on('approval_stock_items')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_stock_item_dokumentasis');
    }
};
