<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_distribusi_details', function (Blueprint $table) {
            $table->enum('status_penerimaan', ['menunggu', 'diterima', 'ditolak'])
                  ->default('menunggu')
                  ->after('catatan');
            $table->text('ulasan')->nullable()->after('status_penerimaan');
        });
    }

    public function down(): void
    {
        Schema::table('order_distribusi_details', function (Blueprint $table) {
            $table->dropColumn(['status_penerimaan', 'ulasan']);
        });
    }
};
