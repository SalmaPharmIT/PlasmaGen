<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByAndModifiedByToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('users', function (Blueprint $table) {
            // Adding the created_by and modified_by columns as nullable unsigned big integers
            $table->unsignedBigInteger('created_by')->nullable()->after('password');
            $table->unsignedBigInteger('modified_by')->nullable()->after('created_by');

            // Adding foreign key constraints
            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('modified_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
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
            // Dropping foreign key constraints first
            $table->dropForeign(['created_by']);
            $table->dropForeign(['modified_by']);

            // Dropping the columns
            $table->dropColumn(['created_by', 'modified_by']);
        });
    }
}
