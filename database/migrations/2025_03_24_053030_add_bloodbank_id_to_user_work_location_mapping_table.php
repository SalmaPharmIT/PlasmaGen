<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBloodbankIdToUserWorkLocationMappingTable extends Migration
{
    public function up()
    {
        Schema::table('user_work_location_mapping', function (Blueprint $table) {
            // Add new bloodbank_id column after country_id, nullable by default
            $table->unsignedBigInteger('bloodbank_id')->nullable()->after('country_id');
            // Set bloodbank_id as a foreign key referencing the id column on the entities table
            $table->foreign('bloodbank_id')
                  ->references('id')->on('entities')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('user_work_location_mapping', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['bloodbank_id']);
            // Then drop the column
            $table->dropColumn('bloodbank_id');
        });
    }
}
