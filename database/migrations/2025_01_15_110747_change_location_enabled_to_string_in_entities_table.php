<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLocationEnabledToStringInEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::table('entities', function (Blueprint $table) {
            // Change 'location_enabled' from boolean to string
            $table->string('location_enabled', 3)->nullable()->change();
        });

        // Update existing records: 1 -> 'yes', 0 -> 'no'
        DB::table('entities')->where('location_enabled', 1)->update(['location_enabled' => 'yes']);
        DB::table('entities')->where('location_enabled', 0)->update(['location_enabled' => 'no']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        Schema::table('entities', function (Blueprint $table) {
            // Revert 'location_enabled' back to boolean
            $table->boolean('location_enabled')->default(false)->change();
        });

        // Update existing records: 'yes' -> 1, 'no' -> 0
        DB::table('entities')->where('location_enabled', 'yes')->update(['location_enabled' => 1]);
        DB::table('entities')->where('location_enabled', 'no')->update(['location_enabled' => 0]);
    }
}
