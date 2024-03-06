<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToStocksTable extends Migration
{
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->text('description')->nullable()->after('type');
        });
    }

    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
