<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUserReportMappingTable extends Migration
{
    public function up()
    {
        Schema::create('user_report_mapping', function (Blueprint $table) {
            // Primary key
            $table->increments('id');

            // Foreign keys from users, roles, and entities tables
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('manager_role_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('employee_role_id');

            // Optional columns for tracking who created/modified the record
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();

            // Timestamps: created_at defaults to current timestamp on insert,
            // updated_at defaults to current timestamp and auto-updates on modification.
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))
                  ->onUpdate(DB::raw('CURRENT_TIMESTAMP'));

            // Soft delete column (defaults to NULL)
            $table->timestamp('deleted_at')->nullable();

            // Define foreign key constraints
            $table->foreign('entity_id')
                  ->references('id')->on('entities')
                  ->onDelete('set null');
            $table->foreign('manager_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('manager_role_id')
                  ->references('id')->on('roles')
                  ->onDelete('cascade');
            $table->foreign('employee_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('employee_role_id')
                  ->references('id')->on('roles')
                  ->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_report_mapping');
    }
}
