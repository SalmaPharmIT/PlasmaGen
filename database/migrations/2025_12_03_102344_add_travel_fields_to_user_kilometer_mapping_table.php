<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTravelFieldsToUserKilometerMappingTable extends Migration
{
    public function up()
    {
        Schema::table('user_kilometer_mapping', function (Blueprint $table) {

            // Add after user_id
            $table->decimal('travel_kms', 10, 2)
                  ->nullable()
                  ->after('user_id');

            $table->decimal('price_per_km', 10, 2)
                  ->nullable()
                  ->after('travel_kms');
        });
    }

    public function down()
    {
        Schema::table('user_kilometer_mapping', function (Blueprint $table) {
            $table->dropColumn(['travel_kms', 'price_per_km']);
        });
    }
}
