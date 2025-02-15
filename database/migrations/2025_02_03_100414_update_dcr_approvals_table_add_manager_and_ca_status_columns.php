<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDcrApprovalsTableAddManagerAndCaStatusColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Rename the column 'vsisit_status' to 'visit_status'
        Schema::table('dcr_approvals', function (Blueprint $table) {
            $table->renameColumn('vsisit_status', 'visit_status');
        });

        // Add new columns after visit_status
        Schema::table('dcr_approvals', function (Blueprint $table) {
            $table->enum('manager_status', ['pending', 'approved', 'rejected', 'accepted', 'declined'])->default('pending')
                  ->after('visit_status');
            $table->unsignedBigInteger('manager_status_by')->nullable()
                  ->after('manager_status');
            $table->enum('ca_status', ['pending', 'approved', 'rejected', 'accepted', 'declined'])->default('pending')
                  ->after('manager_status_by');
            $table->unsignedBigInteger('ca_status_by')->nullable()
                  ->after('ca_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the new columns first
        Schema::table('dcr_approvals', function (Blueprint $table) {
            $table->dropColumn(['manager_status', 'manager_status_by', 'ca_status', 'ca_status_by']);
        });

        // Rename the column 'visit_status' back to 'vsisit_status'
        Schema::table('dcr_approvals', function (Blueprint $table) {
            $table->renameColumn('visit_status', 'vsisit_status');
        });
    }
}
