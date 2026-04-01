<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE user_roles MODIFY COLUMN role_type ENUM('kepala_dapur','ahli_gizi','admin_gudang','super_admin','penerima_mbg','produksi','distributor','mitra','akuntan') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE user_roles MODIFY COLUMN role_type ENUM('kepala_dapur','ahli_gizi','admin_gudang','super_admin','penerima_mbg','produksi','distributor','mitra') NOT NULL");
    }
};
