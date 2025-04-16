<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransportPartnerIdToTransportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('transport_details', function (Blueprint $table) {
            // Only add the column if it does not exist
            if (!Schema::hasColumn('transport_details', 'transport_partner_id')) {
                $table->unsignedBigInteger('transport_partner_id')->nullable()->after('warehouse_id');
                
                // Add the foreign key constraint to the transport_partners table.
                $table->foreign('transport_partner_id')
                      ->references('id')->on('transport_partners')
                      ->onDelete('cascade');
            }
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
            // Drop foreign key first then the column.
            $table->dropForeign(['transport_partner_id']);
            $table->dropColumn('transport_partner_id');
        });
    }
}
