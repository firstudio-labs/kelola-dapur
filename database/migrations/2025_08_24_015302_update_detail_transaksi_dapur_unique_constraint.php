<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_transaksi_dapur', function (Blueprint $table) {
            $table->dropUnique('uk_detail_transaksi_transaksi_menu');
            $table->unique(['id_transaksi', 'id_menu', 'tipe_porsi'], 'uk_detail_transaksi_transaksi_menu_tipe');
        });
    }

    public function down(): void
    {
        Schema::table('detail_transaksi_dapur', function (Blueprint $table) {
            $table->dropUnique('uk_detail_transaksi_transaksi_menu_tipe');
            $table->unique(['id_transaksi', 'id_menu'], 'uk_detail_transaksi_transaksi_menu');
        });
    }
};
