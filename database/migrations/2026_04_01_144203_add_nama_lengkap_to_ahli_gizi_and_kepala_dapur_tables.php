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
        Schema::table('ahli_gizi', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable()->after('id_dapur');
        });

        Schema::table('kepala_dapur', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable()->after('id_dapur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ahli_gizi', function (Blueprint $table) {
            $table->dropColumn('nama_lengkap');
        });

        Schema::table('kepala_dapur', function (Blueprint $table) {
            $table->dropColumn('nama_lengkap');
        });
    }
};
