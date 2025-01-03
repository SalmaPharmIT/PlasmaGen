<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeStateIdNullableInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['state_id']);

            // Modify the 'state_id' column to be nullable
            $table->unsignedBigInteger('state_id')->nullable()->change();

            // Re-add the foreign key constraint
            $table->foreign('state_id')
                  ->references('id')
                  ->on('states')
                  ->onDelete('restrict');
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
            // Drop the foreign key constraint
            $table->dropForeign(['state_id']);

            // Revert the 'state_id' column to be non-nullable
            $table->unsignedBigInteger('state_id')->nullable(false)->change();

            // Re-add the foreign key constraint
            $table->foreign('state_id')
                  ->references('id')
                  ->on('states')
                  ->onDelete('restrict');
        });
    }
}
