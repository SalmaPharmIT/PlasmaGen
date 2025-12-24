<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPlantWarehouseTransferedToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            $table->tinyInteger('is_plant_warehouse_transfered')
                  ->default(0)
                  ->after('collection_warehouse_id');
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
            $table->dropColumn('is_plant_warehouse_transfered');
        });
    }
}
