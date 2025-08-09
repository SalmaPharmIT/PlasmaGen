<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnsToBagStatusDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bag_status_details', function (Blueprint $table) {
            $table->boolean('release_status')->default(0)->after('id');
            $table->boolean('reject_status')->default(0)->after('release_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bag_status_details', function (Blueprint $table) {
            $table->dropColumn('release_status');
            $table->dropColumn('reject_status');
        });
    }
}
