<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTourPlanTimestamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            // Modify 'created_at' to use current timestamp by default
            $table->timestamp('created_at')
                  ->useCurrent()
                  ->change();

            // Modify 'updated_at' to use current timestamp by default and update on update
            $table->timestamp('updated_at')
                  ->useCurrent()
                  ->useCurrentOnUpdate()
                  ->change();
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
            // Revert 'created_at' to nullable without default
            $table->timestamp('created_at')
                  ->nullable()
                  ->default(null)
                  ->change();

            // Revert 'updated_at' to nullable without default and remove on update
            $table->timestamp('updated_at')
                  ->nullable()
                  ->default(null)
                  ->change();
        });
    }
}
