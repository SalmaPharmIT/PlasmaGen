<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeBloodBankIdNullableInTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_plan', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['blood_bank_id']);
            
            // Modify the column to be nullable
            $table->unsignedBigInteger('blood_bank_id')->nullable()->change();
            
            // Re-add the foreign key constraint
            $table->foreign('blood_bank_id')->references('id')->on('entities')->onDelete('cascade');
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
            // Drop the modified foreign key constraint
            $table->dropForeign(['blood_bank_id']);
            
            // Revert the column to be non-nullable
            $table->unsignedBigInteger('blood_bank_id')->nullable(false)->change();
            
            // Re-add the original foreign key constraint
            $table->foreign('blood_bank_id')->references('id')->on('entities')->onDelete('cascade');
        });
    }
}
