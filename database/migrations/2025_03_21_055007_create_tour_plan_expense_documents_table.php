<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourPlanExpenseDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tour_plan_expense_documents', function (Blueprint $table) {
            // Primary Key
            $table->bigIncrements('id');

            // Foreign Key to tour_plan_expenses table
            $table->unsignedBigInteger('tp_expenses_id');

            // Attachments field
            $table->string('attachments');  // File path for the attachment

            // Timestamps (with defaults as requested)
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->onUpdate(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            // Foreign Key Constraints
            $table->foreign('tp_expenses_id')->references('id')->on('tour_plan_expenses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tour_plan_expense_documents');
    }
}
