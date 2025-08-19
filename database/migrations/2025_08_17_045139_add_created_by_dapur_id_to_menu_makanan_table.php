<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByDapurIdToMenuMakananTable extends Migration
{
    public function up()
    {
        Schema::table('menu_makanan', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_dapur_id')->nullable();
            $table->foreign('created_by_dapur_id')
                ->references('id_dapur')
                ->on('dapur')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('menu_makanan', function (Blueprint $table) {
            $table->dropForeign(['created_by_dapur_id']);
            $table->dropColumn('created_by_dapur_id');
        });
    }
}
