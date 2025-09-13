<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id('id_package');
            $table->string('nama_paket');
            $table->text('deskripsi');
            $table->decimal('harga', 10, 0);
            $table->integer('durasi_hari');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_packages');
    }
};
