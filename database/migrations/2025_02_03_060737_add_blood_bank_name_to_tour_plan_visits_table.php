<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBloodBankNameToTourPlanVisitsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tour_plan_visits', function (Blueprint $table) {
            $table->string('blood_bank_name')->nullable()->after('tour_plan_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tour_plan_visits', function (Blueprint $table) {
            $table->dropColumn('blood_bank_name');
        });
    }
}
