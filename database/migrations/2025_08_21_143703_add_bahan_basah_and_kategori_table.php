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
        Schema::table('menu_makanan', function (Blueprint $table) {
            $table->enum('kategori', ['Karbohidrat', 'Lauk', 'Sayur', 'Tambahan'])
                ->after('deskripsi')
                ->default('Lauk');
        });

        Schema::table('bahan_menu', function (Blueprint $table) {
            $table->boolean('is_bahan_basah')
                ->after('jumlah_per_porsi')
                ->default(false)
                ->comment('Apakah bahan ini termasuk bahan basah (berat matang + 7%)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_makanan', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });

        Schema::table('bahan_menu', function (Blueprint $table) {
            $table->dropColumn('is_bahan_basah');
        });
    }
};
