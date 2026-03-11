<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('kategori_prasarana', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('is_active');
        });

        Schema::table('item_prasarana', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('is_active');
        });
    }

    
    public function down(): void
    {
        Schema::table('kategori_prasarana', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });

        Schema::table('item_prasarana', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
