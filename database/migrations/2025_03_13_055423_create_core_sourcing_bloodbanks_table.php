<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCoreSourcingBloodbanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_sourcing_bloodbanks', function (Blueprint $table) {
            // Primary key
            $table->bigIncrements('id');

            // Other Fields
            $table->string('sourcing_blood_bank_name')->nullable();
            $table->string('sourcing_contact_person')->nullable();
            $table->string('sourcing_mobile_number')->nullable();
            $table->string('sourcing_email')->nullable();
            $table->text('sourcing_address')->nullable();
            $table->string('sourcing_ffp_company')->nullable();
            $table->decimal('sourcing_plasma_price', 10, 2)->nullable();
            $table->integer('sourcing_potential_per_month')->nullable();
            $table->text('sourcing_payment_terms')->nullable();
            $table->text('sourcing_remarks')->nullable();

            // Additional columns
            $table->decimal('sourcing_slab1_price', 10, 2)->nullable();
            $table->decimal('sourcing_slab2_price', 10, 2)->nullable();
            $table->decimal('sourcing_slab3_price', 10, 2)->nullable();

            // Timestamps (with defaults as requested)
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->onUpdate(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('core_sourcing_bloodbanks');
    }
}
