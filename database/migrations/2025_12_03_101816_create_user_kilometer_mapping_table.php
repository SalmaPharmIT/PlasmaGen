<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUserKilometerMappingTable extends Migration
{
    public function up()
    {
        Schema::create('user_kilometer_mapping', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            // Foreign Keys
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('user_id');

            // Tracking Columns
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();

            // Timestamps
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')
                  ->default(DB::raw('CURRENT_TIMESTAMP'))
                  ->onUpdate(DB::raw('CURRENT_TIMESTAMP'));

            // Soft Delete
            $table->timestamp('deleted_at')->nullable();

            // Foreign Key Constraints
            $table->foreign('entity_id')
                  ->references('id')->on('entities')
                  ->onDelete('set null');

            $table->foreign('user_id')
                  ->references('id')->on('users')
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
        Schema::dropIfExists('user_kilometer_mapping');
    }
}
