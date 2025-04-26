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
        Schema::create('bag_entries_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bag_entries_id')->constrained('bag_entries')->onDelete('cascade');
            $table->integer('no_of_bags')->nullable();
            $table->integer('bags_in_mini_pool')->nullable();
            $table->string('donor_id')->nullable();
            $table->date('donation_date')->nullable();
            $table->string('blood_group')->nullable();
            $table->integer('bag_volume_ml')->nullable();
            $table->string('tail_cutting')->nullable();
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
        Schema::dropIfExists('bag_entries_details');
    }
}; 