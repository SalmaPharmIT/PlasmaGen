<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReportingToUsersLiveLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_live_locations', function (Blueprint $table) {
            $table->integer('is_reporting')
                  ->default(0)
                  ->after('check_out');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_live_locations', function (Blueprint $table) {
            $table->dropColumn('is_reporting');
        });
    }
}
