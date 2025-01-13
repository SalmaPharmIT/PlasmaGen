<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraColumnsToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('tour_plan', function (Blueprint $table) {
            // 1. tour_plan_type (1 for collections, 2 - sourcing)
            $table->unsignedTinyInteger('tour_plan_type')->nullable()->after('entity_id')->comment('1: Collections, 2: Sourcing');

            // 2. Blood Bank name
            $table->string('blood_bank_name')->nullable()->after('tour_plan_type');

            // 3. sourcing_city_id (foreign key from cities table)
            $table->unsignedBigInteger('sourcing_city_id')->nullable()->after('blood_bank_name');

            // 4. visit_time
            $table->time('visit_time')->nullable()->after('sourcing_city_id');

            // 5. pending_documents_ids (comma separated ids)
            $table->string('pending_documents_ids')->nullable()->after('visit_time');

            // Foreign Key Constraint for sourcing_city_id
            $table->foreign('sourcing_city_id')
                  ->references('id')
                  ->on('cities')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
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
            // Drop Foreign Key Constraint
            $table->dropForeign(['sourcing_city_id']);

            // Drop the columns
            $table->dropColumn([
                'tour_plan_type',
                'blood_bank_name',
                'sourcing_city_id',
                'visit_time',
                'pending_documents_ids',
            ]);
        });
    }
}
