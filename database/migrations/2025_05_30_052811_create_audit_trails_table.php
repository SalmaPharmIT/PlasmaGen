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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('action')->nullable(); // create, update, delete, login, logout, etc.
            $table->string('module')->nullable(); // plasma, barcode, bag entry, etc.
            $table->string('section')->nullable(); // subcategory or specific area of the module
            $table->string('record_id')->nullable(); // ID of the affected record
            $table->text('old_values')->nullable(); // JSON encoded old values
            $table->text('new_values')->nullable(); // JSON encoded new values
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('description')->nullable(); // Human-readable description of the action
            $table->timestamps();

            // Index for faster queries
            $table->index('user_id');
            $table->index('module');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
