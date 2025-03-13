<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollectionRequestFieldsToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            $table->integer('tp_collection_request')
                  ->default(0)
                  ->after('edit_request_reason');
            $table->text('tp_collection_request_message')
                  ->nullable()
                  ->after('tp_collection_request');
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
            $table->dropColumn(['tp_collection_request', 'tp_collection_request_message']);
        });
    }
}
