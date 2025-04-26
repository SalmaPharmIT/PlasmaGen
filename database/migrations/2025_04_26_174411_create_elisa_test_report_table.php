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
        Schema::create('elisa_test_report', function (Blueprint $table) {
            $table->id();
            $table->string('mini_pool_id')->nullable();
            $table->string('well_num')->nullable();
            $table->decimal('od_value', 10, 2)->default(0.00);
            $table->string('result_time')->nullable();
            $table->enum('hbv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('hcv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('hiv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('final_result', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('elisa_test_report');
    }
};
