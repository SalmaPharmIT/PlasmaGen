<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLatLongColumnsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::table('users', function (Blueprint $table) {
            // Rename 'lat' to 'latitude'
            $table->renameColumn('lat', 'latitude');
            
            // Rename 'long' to 'longitude'
            $table->renameColumn('long', 'longitude');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        Schema::table('users', function (Blueprint $table) {
            // Revert 'latitude' back to 'lat'
            $table->renameColumn('latitude', 'lat');
            
            // Revert 'longitude' back to 'long'
            $table->renameColumn('longitude', 'long');
        });
    }
}
