<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dapur_prasarana', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('is_available');
        });

        Schema::create('dapur_prasarana_foto', function (Blueprint $table) {
            $table->id('id_foto');
            $table->unsignedBigInteger('id_dapur_prasarana');
            $table->string('foto_url');
            $table->timestamps();

            $table->foreign('id_dapur_prasarana')->references('id_dapur_prasarana')->on('dapur_prasarana')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dapur_prasarana_foto');

        Schema::table('dapur_prasarana', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
