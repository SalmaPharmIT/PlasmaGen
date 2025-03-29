<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusRemarksToDcrApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dcr_approvals', function (Blueprint $table) {
            // Add the new columns after the respective status columns
            $table->text('manager_status_remarks')->nullable()->after('manager_status_by');
            $table->text('ca_status_remarks')->nullable()->after('ca_status_by');
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
            // Drop the newly added columns
            $table->dropColumn(['manager_status_remarks', 'ca_status_remarks']);
        });
    }
}
