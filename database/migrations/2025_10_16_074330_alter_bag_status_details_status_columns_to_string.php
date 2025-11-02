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
        // Change release_status and reject_status from boolean to varchar
        DB::statement('ALTER TABLE bag_status_details MODIFY release_status VARCHAR(50) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE bag_status_details MODIFY reject_status VARCHAR(50) NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change back to boolean
        DB::statement('ALTER TABLE bag_status_details MODIFY release_status TINYINT(1) NULL DEFAULT 0');
        DB::statement('ALTER TABLE bag_status_details MODIFY reject_status TINYINT(1) NULL DEFAULT 0');
    }
};
