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
        Schema::table('mitra', function (Blueprint $table) {
            $table->string('foto_diri')->nullable()->after('id_user_role');
        });

        Schema::table('penerima_mbg', function (Blueprint $table) {
            $table->string('foto_diri')->nullable()->after('id_user_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitra', function (Blueprint $table) {
            $table->dropColumn('foto_diri');
        });

        Schema::table('penerima_mbg', function (Blueprint $table) {
            $table->dropColumn('foto_diri');
        });
    }
};
