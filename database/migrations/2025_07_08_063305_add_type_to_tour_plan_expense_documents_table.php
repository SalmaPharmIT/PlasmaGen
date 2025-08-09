<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToTourPlanExpenseDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tour_plan_expense_documents', function (Blueprint $table) {
            // add nullable integer 'type' with default 0, after attachments
            $table->integer('type')->default(0)->after('attachments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tour_plan_expense_documents', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
