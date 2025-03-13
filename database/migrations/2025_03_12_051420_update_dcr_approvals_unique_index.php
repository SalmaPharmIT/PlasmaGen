<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDcrApprovalsUniqueIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dcr_approvals', function (Blueprint $table) {
            // Drop the existing unique index on visit_date.
            // The default name is typically dcr_approvals_visit_date_unique.
            $table->dropUnique(['visit_date']);
            
            // Add a composite unique index for visit_date and emp_id.
            $table->unique(['visit_date', 'emp_id']);
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
            // Drop the composite unique index.
            $table->dropUnique(['visit_date', 'emp_id']);
            
            // Restore the unique index on visit_date.
            $table->unique('visit_date');
        });
    }
}
