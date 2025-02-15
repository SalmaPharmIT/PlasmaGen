<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourPlanVisitsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tour_plan_visits', function (Blueprint $table) {
            // Primary key
            $table->bigIncrements('id');

            // Foreign Key to tour_plan table
            $table->unsignedBigInteger('tp_id');

            // Other Fields
            $table->integer('tour_plan_type');  // 1: Collections, 2: Sourcing
            $table->string('sourcing_contact_person')->nullable();
            $table->string('sourcing_mobile_number')->nullable();
            $table->string('sourcing_email')->nullable();
            $table->text('sourcing_address')->nullable();
            $table->string('sourcing_ffp_company')->nullable();
            $table->decimal('sourcing_plasma_price', 10, 2)->nullable();
            $table->integer('sourcing_potential_per_month')->nullable();
            $table->text('sourcing_payment_terms')->nullable();
            $table->text('sourcing_remarks')->nullable();

            // Tracking fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();

            // Timestamps (with defaults as requested)
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->onUpdate(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            // Foreign Key Constraints
            $table->foreign('tp_id')->references('id')->on('tour_plan')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tour_plan_visits');
    }
}
