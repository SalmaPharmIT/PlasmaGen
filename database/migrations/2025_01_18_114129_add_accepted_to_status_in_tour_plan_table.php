<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAcceptedToStatusInTourPlanTable extends Migration
{
    /**
     * The list of ENUM values after adding 'accepted'.
     *
     * @var array
     */
    private $newStatusValues = [
        'initiated',
        'updated',
        'submitted',
        'approved',
        'rejected',
        'dcr_submitted',
        'accepted', // New ENUM value
    ];

    /**
     * The list of ENUM values before adding 'accepted'.
     *
     * @var array
     */
    private $originalStatusValues = [
        'initiated',
        'updated',
        'submitted',
        'approved',
        'rejected',
        'dcr_submitted',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('tour_plan', function (Blueprint $table) {
            // Modify the 'status' ENUM column to include 'accepted'
            $table->enum('status', $this->newStatusValues)
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
            // Revert the 'status' ENUM column to exclude 'accepted'
            $table->enum('status', $this->originalStatusValues)
                  ->default('initiated')
                  ->change();
        });
    }
}
