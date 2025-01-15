<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('transport_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Foreign key to tour_plan table
            $table->unsignedBigInteger('tp_id');
            $table->foreign('tp_id')->references('id')->on('tour_plan')->onDelete('cascade');
            
            $table->string('vehicle_number');
            $table->string('driver_name');
            $table->string('contact_number');
            $table->string('alternative_contact_number')->nullable();
            $table->string('email')->nullable();
            $table->text('remarks')->nullable();
            
            // Foreign keys to users table for created_by and modified_by
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            
            $table->unsignedBigInteger('modified_by');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('restrict');
            
            // Timestamps
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Soft delete
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_details');
    }
}
