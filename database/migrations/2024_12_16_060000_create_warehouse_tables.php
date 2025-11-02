<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration consolidates all warehouse-related tables:
     * - audit_trails
     * - bag_entries, bag_entries_details, bag_entries_mini_pools
     * - bag_status_details
     * - barcode_entries
     * - elisa_test_report, nat_test_report
     * - plasma_entries, plasma_entries_destruction
     * - sub_mini_pool_entries
     */
    public function up(): void
    {
        // 1. Create audit_trails table (no dependencies)
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

            // Indexes for faster queries
            $table->index('user_id');
            $table->index('module');
            $table->index('action');
            $table->index('created_at');
        });

        // 2. Create bag_entries table (depends on entities and users)
        Schema::create('bag_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_bank_id')->nullable()->constrained('entities')->onDelete('set null');
            $table->string('work_station')->nullable();
            $table->date('date')->nullable();
            $table->date('pickup_date')->nullable();
            $table->string('ar_no')->nullable();
            $table->string('grn_no')->nullable();
            $table->string('mega_pool_no')->unique();
            $table->decimal('total_mini_pool_volume', 10, 2)->default(0.00);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Create bag_entries_details table (depends on bag_entries)
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

        // 4. Create bag_entries_mini_pools table (depends on bag_entries)
        Schema::create('bag_entries_mini_pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bag_entries_id')->constrained('bag_entries')->onDelete('cascade');
            $table->json('bag_entries_detail_ids')->nullable();
            $table->decimal('mini_pool_bag_volume', 10, 2)->default(0.00);
            $table->string('mini_pool_number')->nullable()->unique(); // Made unique for foreign key references
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 5. Create bag_status_details table (depends on bag_entries_mini_pools and entities)
        Schema::create('bag_status_details', function (Blueprint $table) {
            $table->id();
            $table->boolean('release_status')->default(0);
            $table->boolean('reject_status')->default(0);
            $table->foreignId('mini_pool_id')->nullable()->constrained('bag_entries_mini_pools')->onDelete('set null');
            $table->foreignId('blood_bank_id')->nullable()->constrained('entities')->onDelete('set null');
            $table->string('ar_no')->nullable();
            $table->string('batch_no')->nullable();
            $table->date('date')->nullable();
            $table->enum('status', ['damage', 'rejection', 'despense'])->nullable();
            $table->decimal('issued_volume', 10, 2)->nullable();
            $table->decimal('total_volume', 10, 2)->nullable();
            $table->decimal('total_issued_volume', 10, 2)->nullable();
            $table->enum('status_type', ['draft', 'final', 'release'])->default('final');
            $table->timestamp('timestamp')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key for deleted_by
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });

        // 6. Create barcode_entries table (no dependencies)
        Schema::create('barcode_entries', function (Blueprint $table) {
            $table->id();
            $table->string('work_station')->nullable();
            $table->string('ar_no')->nullable();
            $table->string('ref_doc_no')->nullable();
            $table->string('mega_pool_no')->nullable();
            $table->string('mini_pool_number')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Add indexes for better performance
            $table->index('mega_pool_no');
            $table->index('mini_pool_number');
        });

        // 7. Create elisa_test_report table (depends on users)
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
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints for user references
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });

        // 8. Create nat_test_report table (depends on users)
        Schema::create('nat_test_report', function (Blueprint $table) {
            $table->id();
            $table->string('mini_pool_id')->nullable();
            $table->enum('hiv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('hbv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('hcv', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->enum('status', ['nonreactive', 'reactive', 'borderline', 'invalid'])->nullable();
            $table->string('result_time')->nullable();
            $table->string('analyzer')->nullable();
            $table->string('operator')->nullable();
            $table->string('flags')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints for user references
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });

        // 9. Create plasma_entries table (depends on entities and users)
        Schema::create('plasma_entries', function (Blueprint $table) {
            $table->id();
            $table->date('pickup_date')->nullable();
            $table->date('reciept_date')->nullable();
            $table->string('grn_no')->nullable();
            $table->foreignId('blood_bank_id')->nullable()->constrained('entities')->onDelete('set null');
            $table->decimal('plasma_qty', 8, 2)->nullable();
            $table->string('alloted_ar_no')->nullable();
            $table->string('destruction_no')->nullable();
            $table->text('remarks')->nullable();
            $table->string('reject_reason')->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });

        // 10. Create plasma_entries_destruction table (depends on entities and users)
        Schema::create('plasma_entries_destruction', function (Blueprint $table) {
            $table->id();
            $table->date('pickup_date')->nullable();
            $table->date('reciept_date')->nullable();
            $table->string('grn_no')->nullable();
            $table->foreignId('blood_bank_id')->nullable()->constrained('entities')->onDelete('set null');
            $table->decimal('plasma_qty', 10, 2)->nullable();
            $table->string('ar_no')->nullable();
            $table->decimal('total_bag_val', 10, 2)->nullable();
            $table->string('destruction_no')->nullable();
            $table->string('donor_id')->nullable();
            $table->string('mega_pool_id')->nullable();
            $table->date('donation_date')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->decimal('bag_volume_ml', 10, 2)->nullable();
            $table->enum('reject_reason', ['Damaged', 'Hemolyzed (Red)', 'Expired', 'Quality Rejected', 're-test', 'quality-rejected'])->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key relationship
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });

        // 11. Create sub_mini_pool_entries table (depends on bag_entries and bag_entries_mini_pools)
        Schema::create('sub_mini_pool_entries', function (Blueprint $table) {
            $table->id();
            $table->string('mega_pool_no')->nullable();
            $table->string('mini_pool_number')->nullable();
            $table->string('sub_mini_pool_no', 255)->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints - reference bag_entries for mega_pool_no
            $table->foreign('mega_pool_no')
                  ->references('mega_pool_no')
                  ->on('bag_entries')
                  ->onDelete('set null');

            // Foreign key constraints - reference bag_entries_mini_pools for mini_pool_number
            $table->foreign('mini_pool_number')
                  ->references('mini_pool_number')
                  ->on('bag_entries_mini_pools')
                  ->onDelete('set null');

            // Foreign key for deleted_by
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });

        // 12. Create entity_settings table
        Schema::create('entity_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->string('ref_no')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->integer('no_of_work_station')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys for user tracking
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
        // Drop tables in reverse order to respect foreign key constraints
        Schema::dropIfExists('sub_mini_pool_entries');
        Schema::dropIfExists('plasma_entries_destruction');
        Schema::dropIfExists('plasma_entries');
        Schema::dropIfExists('nat_test_report');
        Schema::dropIfExists('elisa_test_report');
        Schema::dropIfExists('barcode_entries');
        Schema::dropIfExists('bag_status_details');
        Schema::dropIfExists('bag_entries_mini_pools');
        Schema::dropIfExists('bag_entries_details');
        Schema::dropIfExists('bag_entries');
        Schema::dropIfExists('audit_trails');
        Schema::dropIfExists('entity_settings');
    }
};
