<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('users', function (Blueprint $table) {
            // Adding the country_id column as nullable
            $table->unsignedBigInteger('country_id')->nullable()->after('state_id');

            // Adding foreign key constraint with set null on delete
            $table->foreign('country_id')
                  ->references('id')
                  ->on('countries')
                  ->onDelete('set null'); // Adjust as needed
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
            // Dropping the foreign key constraint first
            $table->dropForeign(['country_id']);

            // Dropping the country_id column
            $table->dropColumn('country_id');
        });
    }
}
