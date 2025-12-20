<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTravelFieldsToDcrApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dcr_approvals', function (Blueprint $table) {

            // Travel Mode
            $table->enum('travel_mode', ['private', 'public'])
                  ->default('public')
                  ->after('ca_status_remarks');

            // KM Travelled
            $table->decimal('km_travelled', 10, 2)
                  ->nullable()
                  ->after('travel_mode');

            // Travel Remarks
            $table->text('travel_remarks')
                  ->nullable()
                  ->after('km_travelled');

            // Approved KM Travel
            $table->decimal('approved_km_travel', 10, 2)
                  ->nullable()
                  ->after('travel_remarks');

            // Approved Travel Cost
            $table->decimal('approved_travel_cost', 10, 2)
                  ->nullable()
                  ->after('approved_km_travel');

            // Approved Travel Remarks
            $table->text('approved_travel_remarks')
                  ->nullable()
                  ->after('approved_travel_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dcr_approvals', function (Blueprint $table) {
            $table->dropColumn([
                'travel_mode',
                'km_travelled',
                'travel_remarks',
                'approved_km_travel',
                'approved_travel_cost',
                'approved_travel_remarks',
            ]);
        });
    }
}
