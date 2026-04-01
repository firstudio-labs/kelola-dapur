<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('period_id');
            $table->date('date');
            $table->tinyInteger('month');
            $table->string('no_bukti')->nullable();
            $table->string('description');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('cash_account_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('period_id')->references('id')->on('accounting_periods')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('accounting_categories');
            $table->foreign('cash_account_id')->references('id')->on('cash_accounts')->onDelete('set null');
            $table->foreign('created_by')->references('id_user')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_transactions');
    }
};
