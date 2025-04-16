<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdToTransportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('transport_details', function (Blueprint $table) {
            // Add the warehouse_id column as an unsignedBigInteger, nullable.
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('remarks');
            
            // Re-add the foreign key constraint to the entities table.
            $table->foreign('warehouse_id')
                  ->references('id')->on('entities')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transport_details', function (Blueprint $table) {
            // Drop the foreign key constraint first.
            $table->dropForeign(['warehouse_id']);
            
            // Then drop the column.
            $table->dropColumn('warehouse_id');
        });
    }
}
