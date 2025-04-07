<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumBoxesNumUnitsNumLitresToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            $table->integer('num_boxes')->after('collection_total_plasma_price')->default(0);
            $table->integer('num_units')->after('num_boxes')->default(0);
            $table->integer('num_litres')->after('num_units')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            $table->dropColumn(['num_boxes', 'num_units', 'num_litres']);
        });
    }
}
