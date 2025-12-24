<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollectionWarehouseIdToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            // Add the column
            $table->unsignedBigInteger('collection_warehouse_id')
                  ->nullable()
                  ->after('transportation_contact_number');

            // Add foreign key constraint
            $table->foreign('collection_warehouse_id')
                  ->references('id')
                  ->on('entities')
                  ->onDelete('set null');
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
            // Drop FK first
            $table->dropForeign(['collection_warehouse_id']);

            // Then drop column
            $table->dropColumn('collection_warehouse_id');
        });
    }
}
