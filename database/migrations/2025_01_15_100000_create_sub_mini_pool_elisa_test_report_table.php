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
        Schema::create('sub_mini_pool_elisa_test_report', function (Blueprint $table) {
            $table->id();
            $table->string('sub_mini_pool_id')->nullable()->index();
            $table->string('mini_pool_number')->nullable();
            $table->string('well_num')->nullable();
            $table->string('od_value')->nullable();
            $table->string('ratio')->nullable();
            $table->string('result_time')->nullable();
            $table->enum('hbv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('hcv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('hiv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('final_result', ['Nonreactive', 'Reactive', 'Borderline', 'Invalid'])->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints for user references
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for better performance
            $table->index('mini_pool_number');
            $table->index('final_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_mini_pool_elisa_test_report');
    }
};

