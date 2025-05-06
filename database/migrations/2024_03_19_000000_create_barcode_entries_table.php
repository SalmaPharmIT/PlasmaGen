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
        Schema::create('barcode_entries', function (Blueprint $table) {
            $table->id();
            $table->string('work_station')->nullable();
            $table->string('ar_no')->nullable();
            $table->string('ref_doc_no')->nullable();
            $table->string('mega_pool_no')->nullable();
            $table->string('mini_pool_number')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_entries');
    }
}; 