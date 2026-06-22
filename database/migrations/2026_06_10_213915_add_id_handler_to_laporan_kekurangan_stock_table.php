<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_kekurangan_stock', function (Blueprint $table) {
            $table->unsignedBigInteger('id_handler')->nullable()->after('id_approval_stock_item');
            $table->foreign('id_handler')
                ->references('id_handler')
                ->on('produksi_handler_bahan')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_kekurangan_stock', function (Blueprint $table) {
            $table->dropForeign(['id_handler']);
            $table->dropColumn(['id_handler']);
        });
    }
};
