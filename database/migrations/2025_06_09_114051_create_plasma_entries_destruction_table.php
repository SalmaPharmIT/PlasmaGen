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
        Schema::create('plasma_entries_destruction', function (Blueprint $table) {
            $table->id();
            $table->date('pickup_date')->nullable()->default(null);
            $table->date('reciept_date')->nullable()->default(null);
            $table->string('grn_no')->nullable()->default(null);
            $table->unsignedBigInteger('blood_bank_id')->nullable()->default(null);
            $table->decimal('plasma_qty', 10, 2)->nullable()->default(null);
            $table->string('destruction_no')->nullable()->default(null);
            $table->enum('reject_reason', ['Damaged', 'Hemolyzed (Red)', 'Expired', 'Quality Rejected'])->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key relationship
            $table->foreign('blood_bank_id')->references('id')->on('entities')->onDelete('set null');
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
        Schema::dropIfExists('plasma_entries_destruction');
    }
};
