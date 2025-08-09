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
            $table->enum('status_type', ['draft', 'final', 'release'])->default('final')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_status_details', function (Blueprint $table) {
            $table->enum('status_type', ['draft', 'final'])->default('final')->change();
        });
    }
};
