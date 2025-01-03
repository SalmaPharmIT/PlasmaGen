<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStatusInTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('tour_plan', function (Blueprint $table) {
            // Modify the 'status' ENUM column to include 'initiated' and 'updated'
            $table->enum('status', ['initiated', 'updated', 'submitted', 'approved', 'rejected'])
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
            // Revert the 'status' ENUM column to its original values and default
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected'])
                  ->default('submitted')
                  ->change();
        });
    }
}
