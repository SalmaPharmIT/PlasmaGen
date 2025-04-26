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
        Schema::create('nat_test_report', function (Blueprint $table) {
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
            $table->timestamps(); // This adds created_at and updated_at
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // Foreign key constraints for user references
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nat_test_report');
    }
}; 