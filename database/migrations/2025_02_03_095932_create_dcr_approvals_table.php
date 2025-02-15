<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDcrApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcr_approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('emp_id');
            $table->date('visit_date')->unique();
            $table->enum('vsisit_status', ['pending', 'dcr_submitted', 'approved', 'rejected', 'accepted']);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')
                  ->default(DB::raw('CURRENT_TIMESTAMP'))
                  ->onUpdate(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            // Foreign key constraints
            $table->foreign('emp_id')->references('id')->on('users')->onDelete('cascade');
            // Optionally, you can also set foreign keys for created_by and modified_by:
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dcr_approvals');
    }
}
