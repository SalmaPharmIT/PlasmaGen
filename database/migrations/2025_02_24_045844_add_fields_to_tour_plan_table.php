<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            $table->enum('tp_status', ['pending', 'submitted', 'accepted', 'rejected'])
                  ->default('pending')
                  ->after('status');
            $table->integer('edit_request')
                  ->default(0)
                  ->after('tp_status');
            $table->text('edit_request_reason')
                  ->nullable()
                  ->after('edit_request');
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
            $table->dropColumn(['tp_status', 'edit_request', 'edit_request_reason']);
        });
    }
}
