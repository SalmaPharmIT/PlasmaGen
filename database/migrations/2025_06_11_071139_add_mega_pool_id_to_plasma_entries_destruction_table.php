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
            $table->string('mega_pool_id')->nullable()->after('donor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plasma_entries_destruction', function (Blueprint $table) {
            $table->dropColumn('mega_pool_id');
        });
    }
};
