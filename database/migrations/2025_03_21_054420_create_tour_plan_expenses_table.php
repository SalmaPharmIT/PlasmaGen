<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourPlanExpensesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tour_plan_expenses', function (Blueprint $table) {
            // Primary Key
            $table->bigIncrements('id');

            // Foreign Key to tour_plan table
            $table->unsignedBigInteger('tp_id');

            // Fields for the expenses table
            $table->date('date')->nullable();  // Date, nullable by default
            $table->text('description')->nullable();  // Description, nullable by default
            $table->decimal('food', 10, 2)->nullable();  // Food expense, nullable by default
            $table->decimal('convention', 10, 2)->nullable();  // Convention expense, nullable by default
            $table->decimal('tel_fax', 10, 2)->nullable();  // Tel/Fax expense, nullable by default
            $table->decimal('lodging', 10, 2)->nullable();  // Lodging expense, nullable by default
            $table->decimal('sundry', 10, 2)->nullable();  // Sundry expense, nullable by default
            $table->decimal('total_price', 10, 2)->nullable();  // Total price, nullable by default

            // Tracking fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();

            // Timestamps (with defaults as requested)
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->onUpdate(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            // Foreign Key Constraints
            $table->foreign('tp_id')->references('id')->on('tour_plan')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tour_plan_expenses');
    }
}
