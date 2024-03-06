<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('current');
            $table->dropColumn('high');
            $table->dropColumn('low');
            $table->dropColumn('percent_change');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->decimal('current', 8, 2)->nullable();
            $table->decimal('high', 8, 2)->nullable();
            $table->decimal('low', 8, 2)->nullable();
            $table->decimal('percent_change', 8, 2)->nullable();
        });
    }
};
