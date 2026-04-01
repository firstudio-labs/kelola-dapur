<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dapur')->nullable();
            $table->string('name');
            $table->enum('type', ['income', 'expense']);
            $table->string('group', 50);
            $table->boolean('is_tax')->default(false);
            $table->timestamps();

            $table->foreign('id_dapur')->references('id_dapur')->on('dapur')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_categories');
    }
};
