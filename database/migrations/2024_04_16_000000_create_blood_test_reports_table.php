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
        Schema::create('blood_test_reports', function (Blueprint $table) {
            $table->id();
            $table->string('minipool_id');
            $table->string('well_number');
            $table->decimal('od_value', 8, 4);
            $table->timestamp('test_timestamp');
            $table->enum('hbv_result', ['nonreactive', 'borderline', 'reactive'])->nullable();
            $table->enum('hcv_result', ['nonreactive', 'borderline', 'reactive'])->nullable();
            $table->enum('hiv_result', ['nonreactive', 'borderline', 'reactive'])->nullable();
            $table->enum('final_result', ['nonreactive', 'borderline', 'reactive']);
            $table->string('file_name');
            $table->string('operator')->nullable();
            $table->string('instrument')->nullable();
            $table->string('protocol')->nullable();
            $table->enum('test_type', ['HBV', 'HCV', 'HIV']);
            $table->string('file_path');
            $table->json('summary')->nullable(); // Stores nonreactive, borderline, reactive counts
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('minipool_id');
            $table->index('test_type');
            $table->index('final_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_test_reports');
    }
}; 