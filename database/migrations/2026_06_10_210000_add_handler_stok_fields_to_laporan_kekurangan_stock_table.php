<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_kekurangan_stock', function (Blueprint $table) {
            $table->text('keterangan_resolve')->nullable()->after('status');
            $table->unsignedBigInteger('id_approval_stock_item')->nullable()->after('keterangan_resolve');
            $table->foreign('id_approval_stock_item')
                ->references('id_approval_stock_item')
                ->on('approval_stock_items')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_kekurangan_stock', function (Blueprint $table) {
            $table->dropForeign(['id_approval_stock_item']);
            $table->dropColumn(['keterangan_resolve', 'id_approval_stock_item']);
        });
    }
};
