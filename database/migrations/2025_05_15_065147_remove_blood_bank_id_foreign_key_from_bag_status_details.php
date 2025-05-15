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
            $table->dropForeign(['blood_bank_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_status_details', function (Blueprint $table) {
            $table->foreign('blood_bank_id')
                  ->references('id')
                  ->on('plasma_entries')
                  ->onDelete('set null');
        });
    }
};
