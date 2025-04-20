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
            $table->string('blood_centre');
            $table->string('work_station');
            $table->date('date');
            $table->date('pickup_date');
            $table->string('ar_no');
            $table->string('grn_no');
            $table->string('mega_pool_no');
            $table->json('bag_details'); // Will store all bag details as JSON
            $table->decimal('total_volume', 10, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
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
