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
        Schema::table('order_distribusi_details', function (Blueprint $table) {
            $table->text('kritik')->nullable()->after('ulasan');
            $table->string('kritik_foto')->nullable()->after('kritik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_distribusi_details', function (Blueprint $table) {
            $table->dropColumn(['kritik', 'kritik_foto']);
        });
    }
};
