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
        Schema::table('order_produksi', function (Blueprint $table) {
            $table->text('ulasan')->nullable()->after('catatan');
            $table->string('ulasan_foto')->nullable()->after('ulasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_produksi', function (Blueprint $table) {
            $table->dropColumn(['ulasan', 'ulasan_foto']);
        });
    }
};
