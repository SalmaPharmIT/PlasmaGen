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
        // Create nat_re_test_report table (same structure as nat_test_report)
        Schema::create('nat_re_test_report', function (Blueprint $table) {
            $table->id();
            $table->string('mini_pool_id')->nullable();
            $table->enum('hiv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('hbv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('hcv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('status', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->string('result_time')->nullable();
            $table->string('analyzer')->nullable();
            $table->string('operator')->nullable();
            $table->string('flags')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->boolean('is_retest')->default(true); // Always true for retest records
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints for user references
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            // Add indexes for better performance
            $table->index('mini_pool_id');
            $table->index('is_retest');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nat_re_test_report');
    }
};
