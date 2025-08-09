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
            // Remove pickup_date column
            $table->dropColumn('pickup_date');
            
            // Add new columns
            $table->string('batch_no')->nullable()->after('ar_no');
            $table->date('date')->nullable()->after('batch_no');
            $table->decimal('issued_volume', 10, 2)->nullable()->after('status');
            $table->decimal('total_volume', 10, 2)->nullable()->after('issued_volume');
            $table->decimal('total_issued_volume', 10, 2)->nullable()->after('total_volume');
            $table->enum('status_type', ['draft', 'final'])->default('final')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_status_details', function (Blueprint $table) {
            // Revert changes - add pickup_date back
            $table->date('pickup_date')->nullable()->after('blood_bank_id');
            
            // Remove the new columns
            $table->dropColumn([
                'batch_no',
                'date',
                'issued_volume',
                'total_volume',
                'total_issued_volume',
                'status_type'
            ]);
        });
    }
};
