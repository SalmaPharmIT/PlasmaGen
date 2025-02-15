<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcrAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcr_attachments', function (Blueprint $table) {
            // 1. Primary Key
            $table->bigIncrements('id');
            
            // 2. Foreign Key from tour_plan table
            $table->unsignedBigInteger('tp_id');
            
            // 3. Tour Plan Type
            $table->unsignedTinyInteger('tour_plan_type')->comment('1: Collections, 2: Sourcing');
            
            // 4. Attachment Type
            $table->unsignedTinyInteger('attachment_type')->comment('1: Certificate of Quality, 2: Donor Report, 3: Invoice Copy, 4: Pending Documents');
            
            // 5. Attachments
            $table->string('attachments');
            
            // 6. Created By (Foreign Key from users table, nullable)
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            
            // 7. Modified By (Foreign Key from users table, nullable)
            $table->unsignedBigInteger('modified_by')->nullable()->default(null);
            
            // 8. Timestamps
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // 9. Soft Deletes
            $table->softDeletes();
            
            // Foreign Key Constraints
            $table->foreign('tp_id')->references('id')->on('tour_plan')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dcr_attachments');
    }
}
