<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillAvailableToTourPlanExpensesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tour_plan_expenses', function (Blueprint $table) {
            // add boolean bill_available column, default false
            $table->boolean('bill_available')->default(false)->after('dcr_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tour_plan_expenses', function (Blueprint $table) {
            $table->dropColumn('bill_available');
        });
    }
}
