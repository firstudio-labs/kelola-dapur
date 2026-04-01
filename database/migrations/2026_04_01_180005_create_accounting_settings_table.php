<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dapur')->unique();
            $table->string('institution_name')->nullable();
            $table->text('address')->nullable();
            $table->string('head_name')->nullable();
            $table->string('treasurer_name')->nullable();
            $table->string('foundation_name')->nullable();
            $table->string('foundation_head')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('report_location')->nullable();
            $table->date('report_date')->nullable();
            $table->timestamps();

            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_settings');
    }
};
