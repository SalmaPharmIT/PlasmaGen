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
            $table->enum('release_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('reject_status', ['pending', 'approved', 'rejected'])->default('pending');
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
