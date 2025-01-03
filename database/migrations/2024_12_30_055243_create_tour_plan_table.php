<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Foreign Keys
            $table->unsignedBigInteger('blood_bank_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('entity_id');
            
            // Other Fields
            $table->date('visit_date');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected'])->default('submitted');
            $table->string('client_type')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('blood_bank_id')->references('id')->on('entities')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade');
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
        Schema::dropIfExists('tour_plan');
    }
}
