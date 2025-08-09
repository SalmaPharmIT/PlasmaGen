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
            $table->string('donor_id')->nullable()->after('destruction_no');
            $table->date('donation_date')->nullable()->after('donor_id');
            $table->string('blood_group', 5)->nullable()->after('donation_date');
            $table->decimal('bag_volume_ml', 10, 2)->nullable()->after('blood_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plasma_entries_destruction', function (Blueprint $table) {
            $table->dropColumn('donor_id');
            $table->dropColumn('donation_date');
            $table->dropColumn('blood_group');
            $table->dropColumn('bag_volume_ml');
        });
    }
};
