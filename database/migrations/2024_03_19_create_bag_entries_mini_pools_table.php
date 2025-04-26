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
        Schema::create('bag_entries_mini_pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bag_entries_id')->constrained('bag_entries')->onDelete('cascade');
            $table->json('bag_entries_detail_ids')->nullable();
            $table->decimal('mini_pool_bag_volume', 10, 2)->default(0.00);
            $table->string('mini_pool_number')->nullable();
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
        Schema::dropIfExists('bag_entries_mini_pools');
    }
}; 