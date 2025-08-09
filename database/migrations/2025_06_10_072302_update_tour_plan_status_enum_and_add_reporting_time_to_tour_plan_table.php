<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTourPlanStatusEnumAndAddReportingTimeToTourPlanTable extends Migration
{
    /**
     * The ENUM values before this migration (includes 'accepted').
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
        'accepted',
    ];

    /**
     * The ENUM values after adding 'cancel_requested' and 'cancel_approved'.
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
        'accepted',
        'cancel_requested',
        'cancel_approved',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            // 1) Add the two new ENUM values to 'status'
            $table->enum('status', $this->newStatusValues)
                  ->default('initiated')
                  ->change();

            // 2) Add a nullable timestamp column 'reporting_time'
            $table->timestamp('reporting_time')
                  ->nullable()
                  ->after('status');
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
            // 1) Revert 'status' back to the previous set (with 'accepted' but without the two new ones)
            $table->enum('status', $this->originalStatusValues)
                  ->default('initiated')
                  ->change();

            // 2) Drop the 'reporting_time' column
            $table->dropColumn('reporting_time');
        });
    }
}
