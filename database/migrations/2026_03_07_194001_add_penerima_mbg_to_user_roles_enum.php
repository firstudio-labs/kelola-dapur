<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE user_roles MODIFY COLUMN role_type ENUM('kepala_dapur','ahli_gizi','admin_gudang','super_admin','penerima_mbg') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE user_roles MODIFY COLUMN role_type ENUM('kepala_dapur','ahli_gizi','admin_gudang','super_admin') NOT NULL");
    }
};
