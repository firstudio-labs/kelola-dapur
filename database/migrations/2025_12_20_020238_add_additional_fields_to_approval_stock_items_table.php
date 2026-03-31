<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('approval_stock_items', function (Blueprint $table) {
            $table->time('jam_kedatangan')->nullable()->after('keterangan');
            $table->date('tanggal_produksi')->nullable()->after('jam_kedatangan');
            $table->date('tanggal_expired')->nullable()->after('tanggal_produksi');
            $table->decimal('suhu_bahan_makanan', 5, 2)->nullable()->after('tanggal_expired')->comment('Suhu dalam celcius');
            $table->string('warna_bahan_makanan', 50)->nullable()->after('suhu_bahan_makanan');
            $table->string('foto_bahan', 255)->nullable()->after('warna_bahan_makanan');
        });
    }

    public function down(): void
    {
        Schema::table('approval_stock_items', function (Blueprint $table) {
            $table->dropColumn([
                'jam_kedatangan',
                'tanggal_produksi',
                'tanggal_expired',
                'suhu_bahan_makanan',
                'warna_bahan_makanan',
                'foto_bahan'
            ]);
        });
    }
};
