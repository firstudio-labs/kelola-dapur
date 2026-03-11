<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        DB::statement("ALTER TABLE user_roles MODIFY COLUMN role_type ENUM('kepala_dapur','ahli_gizi','admin_gudang','super_admin','penerima_mbg','produksi') NOT NULL");
    }

    
    public function down(): void
    {
        DB::statement("ALTER TABLE user_roles MODIFY COLUMN role_type ENUM('kepala_dapur','ahli_gizi','admin_gudang','super_admin','penerima_mbg') NOT NULL");
    }
};
