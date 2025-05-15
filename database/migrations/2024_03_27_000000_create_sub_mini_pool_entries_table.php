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
        Schema::create('sub_mini_pool_entries', function (Blueprint $table) {
            $table->id();
            $table->string('mega_pool_no')->nullable();
            $table->string('mini_pool_number')->nullable();
            $table->string('sub_mini_pool_no', 255)->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('mega_pool_no')
                  ->references('mega_pool_no')
                  ->on('barcode_entries')
                  ->onDelete('set null');

            $table->foreign('mini_pool_number')
                  ->references('mini_pool_number')
                  ->on('barcode_entries')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_mini_pool_entries');
    }
}; 