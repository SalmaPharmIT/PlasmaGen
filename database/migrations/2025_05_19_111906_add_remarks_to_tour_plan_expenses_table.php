<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemarksToTourPlanExpensesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tour_plan_expenses', function (Blueprint $table) {
            // add remarks column after total_price (or wherever you prefer)
            $table->text('remarks')->nullable()->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tour_plan_expenses', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
}
