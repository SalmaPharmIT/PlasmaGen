<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            // Ensure the 'remaining_quantity' column exists before adding 'price'
            if (Schema::hasColumn('tour_plan', 'remaining_quantity')) {
                $table->decimal('price', 10, 2)->nullable()->after('remaining_quantity');
            } else {
                // If 'remaining_quantity' does not exist, add 'price' at the end
                $table->decimal('price', 10, 2)->nullable();
            }
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
            $table->dropColumn('price');
        });
    }
}
