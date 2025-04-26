<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bag_entries', function (Blueprint $table) {
            $table->decimal('total_mini_pool_volume', 10, 2)->default(0.00)->after('mega_pool_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_entries', function (Blueprint $table) {
            $table->dropColumn('total_mini_pool_volume');
        });
    }
}; 