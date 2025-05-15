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
        Schema::create('bag_status_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mini_pool_id')->nullable();
            $table->unsignedBigInteger('blood_bank_id')->nullable();
            $table->date('pickup_date')->nullable();
            $table->string('ar_no')->nullable();
            $table->enum('status', ['damage', 'rejection', 'despense'])->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // Foreign key constraints
            $table->foreign('mini_pool_id')->references('id')->on('barcode_entries')->onDelete('set null');
            $table->foreign('blood_bank_id')->references('id')->on('plasma_entries')->onDelete('set null');
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
        Schema::dropIfExists('bag_status_details');
    }
}; 