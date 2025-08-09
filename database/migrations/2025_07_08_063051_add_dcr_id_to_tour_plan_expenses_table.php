<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDcrIdToTourPlanExpensesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tour_plan_expenses', function (Blueprint $table) {
            // add nullable dcr_id column
            $table->unsignedBigInteger('dcr_id')->nullable()->after('tp_id');

            // foreign key to dcr_approvals(id)
            $table
                ->foreign('dcr_id')
                ->references('id')
                ->on('dcr_approvals')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tour_plan_expenses', function (Blueprint $table) {
            // drop foreign key and column
            $table->dropForeign(['dcr_id']);
            $table->dropColumn('dcr_id');
        });
    }
}
