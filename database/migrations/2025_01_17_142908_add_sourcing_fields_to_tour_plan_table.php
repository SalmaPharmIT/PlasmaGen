<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourcingFieldsToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('tour_plan', function (Blueprint $table) {
            $table->string('sourcing_contact_person')->nullable()->after('remarks');
            $table->string('sourcing_mobile_number')->nullable()->after('sourcing_contact_person');
            $table->string('sourcing_email')->nullable()->after('sourcing_mobile_number');
            $table->text('sourcing_address')->nullable()->after('sourcing_email');
            $table->string('sourcing_ffp_company')->nullable()->after('sourcing_address');
            $table->decimal('sourcing_plasma_price', 10, 2)->nullable()->after('sourcing_ffp_company');
            $table->integer('sourcing_potential_per_month')->nullable()->after('sourcing_plasma_price');
            $table->string('sourcing_payment_terms')->nullable()->after('sourcing_potential_per_month');
            $table->text('sourcing_remarks')->nullable()->after('sourcing_payment_terms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   
        Schema::table('tour_plan', function (Blueprint $table) {
            $table->dropColumn([
                'sourcing_contact_person',
                'sourcing_mobile_number',
                'sourcing_email',
                'sourcing_address',
                'sourcing_ffp_company',
                'sourcing_plasma_price',
                'sourcing_potential_per_month',
                'sourcing_payment_terms',
                'sourcing_remarks',
            ]);
        });
    }
}
