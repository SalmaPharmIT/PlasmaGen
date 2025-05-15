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
        Schema::table('plasma_entries', function (Blueprint $table) {
            $table->date('pickup_date')->after('id')->nullable();
            $table->decimal('plasma_qty', 8, 2)->after('blood_bank_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plasma_entries', function (Blueprint $table) {
            $table->dropColumn(['pickup_date', 'plasma_qty']);
        });
    }
}; 