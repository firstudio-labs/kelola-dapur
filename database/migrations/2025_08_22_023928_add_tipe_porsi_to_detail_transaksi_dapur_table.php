<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_transaksi_dapur', function (Blueprint $table) {
            $table->enum('tipe_porsi', ['besar', 'kecil'])->after('jumlah_porsi');
            $table->index(['tipe_porsi'], 'idx_detail_transaksi_tipe_porsi');
        });
    }

    public function down(): void
    {
        Schema::table('detail_transaksi_dapur', function (Blueprint $table) {
            $table->dropIndex(['tipe_porsi']);
            $table->dropColumn('tipe_porsi');
        });
    }
};
