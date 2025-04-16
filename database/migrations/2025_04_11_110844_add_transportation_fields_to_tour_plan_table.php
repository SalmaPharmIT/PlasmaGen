<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransportationFieldsToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            // Add a boolean field for other_transportation, default false
            $table->boolean('other_transportation')->default(false)->after('client_type');
            
            // Add a transportation_name field as a string (nullable, defaults to null)
            $table->string('transportation_name')->nullable()->after('other_transportation');
            
            // Add a transportation_contact_person field as a string (nullable, defaults to null)
            $table->string('transportation_contact_person')->nullable()->after('transportation_name');
            
            // Add a transportation_contact_number field as a string (nullable, defaults to null)
            $table->string('transportation_contact_number')->nullable()->after('transportation_contact_person');
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
            $table->dropColumn([
                'other_transportation', 
                'transportation_name', 
                'transportation_contact_person', 
                'transportation_contact_number'
            ]);
        });
    }
}
