<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDcrSubmittedStatusToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('tour_plan', function (Blueprint $table) {
            // Modify the 'status' ENUM column to include 'dcr_submitted'
            $table->enum('status', ['initiated', 'updated', 'submitted', 'approved', 'rejected', 'dcr_submitted'])
                  ->default('initiated')
                  ->change();
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
            // Revert the 'status' ENUM column to exclude 'dcr_submitted'
            $table->enum('status', ['initiated', 'updated', 'submitted', 'approved', 'rejected'])
                  ->default('initiated')
                  ->change();
        });
    }
}
