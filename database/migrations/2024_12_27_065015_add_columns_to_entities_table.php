<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->string('contact_person')->nullable();
            $table->string('FFP_rocurement_company')->nullable();
            $table->string('final_accepted_offer')->nullable();
            $table->string('payment_terms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'FFP_rocurement_company', 'final_accepted_offer', 'payment_terms']);
        });
    }
}
