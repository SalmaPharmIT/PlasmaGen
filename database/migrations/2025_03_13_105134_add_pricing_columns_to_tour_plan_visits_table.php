<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingColumnsToTourPlanVisitsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tour_plan_visits', function (Blueprint $table) {
            $table->decimal('sourcing_part_a_price', 10, 2)->nullable()->after('sourcing_remarks');
            $table->decimal('sourcing_part_b_price', 10, 2)->nullable()->after('sourcing_part_a_price');
            $table->decimal('sourcing_part_c_price', 10, 2)->nullable()->after('sourcing_part_b_price');
            $table->boolean('include_gst')->default(0)->after('sourcing_part_c_price');
            $table->integer('gst_rate')->default(0)->after('include_gst');
            $table->decimal('sourcing_total_plasma_price', 10, 2)->nullable()->after('gst_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tour_plan_visits', function (Blueprint $table) {
            $table->dropColumn([
                'sourcing_part_a_price',
                'sourcing_part_b_price',
                'sourcing_part_c_price',
                'include_gst',
                'gst_rate',
                'sourcing_total_plasma_price'
            ]);
        });
    }
}
