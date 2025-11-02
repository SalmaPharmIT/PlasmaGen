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
        Schema::table('bag_status_details', function (Blueprint $table) {
            // Drop foreign key constraint first if it exists
            try {
                $table->dropForeign(['mini_pool_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        });

        // Change the column type to string
        DB::statement('ALTER TABLE bag_status_details MODIFY mini_pool_id VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change back to unsignedBigInteger
        DB::statement('ALTER TABLE bag_status_details MODIFY mini_pool_id BIGINT UNSIGNED NULL');

        Schema::table('bag_status_details', function (Blueprint $table) {
            // Restore foreign key constraint
            $table->foreign('mini_pool_id')->references('id')->on('bag_entries_mini_pools')->onDelete('set null');
        });
    }
};
