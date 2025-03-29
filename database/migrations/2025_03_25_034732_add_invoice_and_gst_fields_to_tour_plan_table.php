<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceAndGstFieldsToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            $table->decimal('part_a_invoice_price', 10, 2)->nullable()->after('price');
            $table->decimal('part_b_invoice_price', 10, 2)->nullable()->after('part_a_invoice_price');
            $table->decimal('part_c_invoice_price', 10, 2)->nullable()->after('part_b_invoice_price');
            $table->boolean('include_gst')->default(0)->after('part_c_invoice_price');
            $table->integer('gst_rate')->default(0)->after('include_gst');
            $table->decimal('collection_total_plasma_price', 10, 2)->nullable()->after('gst_rate');
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
                'part_a_invoice_price', 
                'part_b_invoice_price', 
                'part_c_invoice_price', 
                'include_gst', 
                'gst_rate', 
                'collection_total_plasma_price'
            ]);
        });
    }
}
