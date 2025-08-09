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
        // First, get all the existing data to preserve it
        $existingData = DB::table('bag_status_details')->select('id', 'mini_pool_id')->get();
        
        Schema::table('bag_status_details', function (Blueprint $table) {
            // Drop the existing column
            $table->dropColumn('mini_pool_id');
        });
        
        Schema::table('bag_status_details', function (Blueprint $table) {
            // Recreate as string
            $table->string('mini_pool_id')->nullable()->after('id');
        });
        
        // Restore the data
        foreach ($existingData as $row) {
            if ($row->mini_pool_id !== null) {
                DB::table('bag_status_details')
                    ->where('id', $row->id)
                    ->update(['mini_pool_id' => (string)$row->mini_pool_id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, get all the existing data to preserve it
        $existingData = DB::table('bag_status_details')->select('id', 'mini_pool_id')->get();
        
        Schema::table('bag_status_details', function (Blueprint $table) {
            // Drop the string column
            $table->dropColumn('mini_pool_id');
        });
        
        Schema::table('bag_status_details', function (Blueprint $table) {
            // Recreate as bigInteger
            $table->unsignedBigInteger('mini_pool_id')->nullable()->after('id');
        });
        
        // Restore the data (only numeric values can be converted back)
        foreach ($existingData as $row) {
            if ($row->mini_pool_id !== null && is_numeric($row->mini_pool_id)) {
                DB::table('bag_status_details')
                    ->where('id', $row->id)
                    ->update(['mini_pool_id' => (int)$row->mini_pool_id]);
            }
        }
    }
};
