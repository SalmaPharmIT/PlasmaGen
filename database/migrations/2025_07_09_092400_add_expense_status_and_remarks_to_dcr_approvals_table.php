<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpenseStatusAndRemarksToDcrApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dcr_approvals', function (Blueprint $table) {
            // Expense manager status
            $table->enum('expense_mgr_status', ['pending', 'cleared', 'rejected'])
                  ->default('pending')
                  ->after('ca_status_by');

            // Expense manager remarks
            $table->text('expense_mgr_remarks')
                  ->nullable()
                  ->after('expense_mgr_status');

            // Expense CA status
            $table->enum('expense_ca_status', ['pending', 'cleared', 'rejected'])
                  ->default('pending')
                  ->after('expense_mgr_remarks');

            // Expense CA remarks
            $table->text('expense_ca_remarks')
                  ->nullable()
                  ->after('expense_ca_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dcr_approvals', function (Blueprint $table) {
            $table->dropColumn([
                'expense_mgr_status',
                'expense_mgr_remarks',
                'expense_ca_status',
                'expense_ca_remarks',
            ]);
        });
    }
}
