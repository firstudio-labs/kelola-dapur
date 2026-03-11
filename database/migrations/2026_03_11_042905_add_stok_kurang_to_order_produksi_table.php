<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE order_produksi MODIFY COLUMN status ENUM('stok_kurang','belum_dibuat','sedang_dibuat','selesai') NOT NULL DEFAULT 'belum_dibuat'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE order_produksi MODIFY COLUMN status ENUM('belum_dibuat','sedang_dibuat','selesai') NOT NULL DEFAULT 'belum_dibuat'");
    }
};
