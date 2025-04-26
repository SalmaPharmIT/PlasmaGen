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
        Schema::create('bag_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_bank_id')->nullable()->constrained('entities')->onDelete('set null');
            $table->string('work_station')->nullable();
            $table->date('date')->nullable();
            $table->date('pickup_date')->nullable();
            $table->string('ar_no')->nullable();
            $table->string('grn_no')->nullable();
            $table->string('mega_pool_no')->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_entries');
    }
}; 