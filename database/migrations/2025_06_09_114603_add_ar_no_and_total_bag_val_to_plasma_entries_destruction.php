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
        Schema::table('plasma_entries_destruction', function (Blueprint $table) {
            $table->string('ar_no')->nullable()->after('plasma_qty');
            $table->decimal('total_bag_val', 10, 2)->nullable()->default(null)->after('ar_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plasma_entries_destruction', function (Blueprint $table) {
            $table->dropColumn('ar_no');
            $table->dropColumn('total_bag_val');
        });
    }
};
