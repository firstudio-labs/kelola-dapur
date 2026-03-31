<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        
        DB::statement('ALTER TABLE `order_distribusi_details` CHANGE `id_detail_distribusi` `id_detail` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE `order_distribusi_details` CHANGE `jumlah_porsi_dikirim` `jumlah_diterima` INT NOT NULL DEFAULT 0');

        Schema::table('order_distribusi_details', function (Blueprint $table) {
            $toDrop = [];
            if (Schema::hasColumn('order_distribusi_details', 'path_foto')) {
                $toDrop[] = 'path_foto';
            }
            if (Schema::hasColumn('order_distribusi_details', 'waktu_dikirim')) {
                $toDrop[] = 'waktu_dikirim';
            }
            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `order_distribusi_details` CHANGE `id_detail` `id_detail_distribusi` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE `order_distribusi_details` CHANGE `jumlah_diterima` `jumlah_porsi_dikirim` INT NOT NULL DEFAULT 0');

        Schema::table('order_distribusi_details', function (Blueprint $table) {
            $table->string('path_foto')->nullable();
            $table->timestamp('waktu_dikirim')->nullable();
        });
    }
};
