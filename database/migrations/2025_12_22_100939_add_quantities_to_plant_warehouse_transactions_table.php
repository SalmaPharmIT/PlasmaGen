<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantitiesToPlantWarehouseTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('plant_warehouse_transactions', function (Blueprint $table) {

            $table->integer('num_boxes')
                  ->default(0)
                  ->after('tour_plan_ids');

            $table->integer('num_units')
                  ->default(0)
                  ->after('num_boxes');

            $table->integer('num_litres')
                  ->default(0)
                  ->after('num_units');
        });
    }

    public function down()
    {
        Schema::table('plant_warehouse_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'num_boxes',
                'num_units',
                'num_litres'
            ]);
        });
    }
}
