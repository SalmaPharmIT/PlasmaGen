<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter the ENUM values in the reject_reason column
        DB::statement("ALTER TABLE plasma_entries_destruction MODIFY COLUMN reject_reason ENUM('Damaged', 'Hemolyzed (Red)', 'Expired', 'Quality Rejected', 're-test', 'quality-rejected')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the original ENUM values
        DB::statement("ALTER TABLE plasma_entries_destruction MODIFY COLUMN reject_reason ENUM('Damaged', 'Hemolyzed (Red)', 'Expired', 'Quality Rejected')");
    }
};
