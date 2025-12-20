<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToNonFieldWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('non_field_works', function (Blueprint $table) {
            // Adding soft delete column
            $table->softDeletes()->after('modified_by'); // creates deleted_at nullable
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('non_field_works', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
}
