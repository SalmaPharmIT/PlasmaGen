<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyGenderEnumInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('users', function (Blueprint $table) {
            // Modify the 'gender' column to use lowercase enum values
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->change();
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
            // Revert the 'gender' column to original enum values
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->change();
        });
    }
}
