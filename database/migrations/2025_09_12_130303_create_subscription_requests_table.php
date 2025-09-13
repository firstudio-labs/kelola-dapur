<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscription_requests', function (Blueprint $table) {
            $table->id('id_subscription_request');
            $table->foreignId('id_dapur')->constrained('dapur', 'id_dapur')->onDelete('cascade');
            $table->foreignId('id_package')->constrained('subscription_packages', 'id_package')->onDelete('cascade');
            $table->foreignId('id_promo')->nullable()->constrained('promo_codes', 'id_promo')->onDelete('set null');
            $table->decimal('harga_asli', 10, 0);
            $table->decimal('diskon', 10, 0)->default(0);
            $table->decimal('harga_final', 10, 0); // harga dengan id_dapur ditambahkan
            $table->string('bukti_transfer')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_request')->useCurrent();
            $table->timestamp('tanggal_approval')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_requests');
    }
};
