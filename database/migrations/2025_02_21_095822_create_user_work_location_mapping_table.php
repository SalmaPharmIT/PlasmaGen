<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUserWorkLocationMappingTable extends Migration
{
    public function up()
    {
        Schema::create('user_work_location_mapping', function (Blueprint $table) {
            // Primary key
            $table->increments('id');

            // Foreign key columns
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('country_id');

            // Tracking columns
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();

            // Timestamps
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')
                  ->default(DB::raw('CURRENT_TIMESTAMP'))
                  ->onUpdate(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            // Define foreign key constraints
            $table->foreign('entity_id')
                  ->references('id')->on('entities')
                  ->onDelete('set null');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('city_id')
                  ->references('id')->on('cities')
                  ->onDelete('cascade');
            $table->foreign('state_id')
                  ->references('id')->on('states')
                  ->onDelete('cascade');
            $table->foreign('country_id')
                  ->references('id')->on('countries')
                  ->onDelete('cascade');
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');
            $table->foreign('modified_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_work_location_mapping');
    }
}
