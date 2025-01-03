<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityFieldsToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('tour_plan', function (Blueprint $table) {
            $table->integer('quantity')->after('status')->default(0);
            $table->integer('available_quantity')->after('quantity')->default(0);
            $table->integer('remaining_quantity')->after('available_quantity')->default(0);
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
            $table->dropColumn(['quantity', 'available_quantity', 'remaining_quantity']);
        });
    }
}
