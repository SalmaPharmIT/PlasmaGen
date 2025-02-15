<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKmBoundAndLocationEnabledToEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::table('entities', function (Blueprint $table) {
            // Add 'km_bound' column as decimal, nullable
            $table->decimal('km_bound', 8, 2)->nullable()->after('name'); // Adjust precision and scale as needed

            // Add 'location_enabled' column as boolean, nullable
            $table->boolean('location_enabled')->nullable()->after('km_bound');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        Schema::table('entities', function (Blueprint $table) {
            // Drop the columns if rolling back
            $table->dropColumn(['km_bound', 'location_enabled']);
        });
    }
}
