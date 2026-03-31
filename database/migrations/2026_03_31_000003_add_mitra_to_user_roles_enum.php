<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite does not support ALTER COLUMN for enum, 
        // but the existing code uses string type not native enum.
        // We check if it's SQLite first (for local dev).
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite stores role_type as text, no change needed
            return;
        }

        DB::statement("ALTER TABLE user_roles MODIFY COLUMN role_type ENUM(
            'super_admin',
            'kepala_dapur',
            'admin_gudang',
            'ahli_gizi',
            'penerima_mbg',
            'produksi',
            'distributor',
            'mitra'
        ) NOT NULL");
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE user_roles MODIFY COLUMN role_type ENUM(
            'super_admin',
            'kepala_dapur',
            'admin_gudang',
            'ahli_gizi',
            'penerima_mbg',
            'produksi',
            'distributor'
        ) NOT NULL");
    }
};
