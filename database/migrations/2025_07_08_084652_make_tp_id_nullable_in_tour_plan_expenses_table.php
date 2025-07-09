<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeTpIdNullableInTourPlanExpensesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tour_plan_expenses', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['tp_id']);

            // Modify tp_id to be nullable
            $table->unsignedBigInteger('tp_id')->nullable()->change();

            // Re-add the foreign key (now allowing nulls)
            $table
                ->foreign('tp_id')
                ->references('id')
                ->on('tour_plan')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tour_plan_expenses', function (Blueprint $table) {
            // Drop the nullable FK
            $table->dropForeign(['tp_id']);

            // Revert tp_id back to not nullable
            $table->unsignedBigInteger('tp_id')->nullable(false)->change();

            // Re-add the original foreign key
            $table
                ->foreign('tp_id')
                ->references('id')
                ->on('tour_plan')
                ->onDelete('cascade');
        });
    }
}
