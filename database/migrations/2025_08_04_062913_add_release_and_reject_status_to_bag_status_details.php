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
        Schema::table('bag_status_details', function (Blueprint $table) {
            $table->boolean('release_status')->default(0)->after('id');
            $table->boolean('reject_status')->default(0)->after('release_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_status_details', function (Blueprint $table) {
            $table->dropColumn('release_status');
            $table->dropColumn('reject_status');
        });
    }
};
