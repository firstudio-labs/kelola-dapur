<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id('id_promo');
            $table->string('kode_promo')->unique();
            $table->integer('persentase_diskon');
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('promo_codes');
    }
};
