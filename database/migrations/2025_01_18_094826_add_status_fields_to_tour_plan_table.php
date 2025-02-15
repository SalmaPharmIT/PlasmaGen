<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusFieldsToTourPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('tour_plan', function (Blueprint $table) {
            // Adding manager_status enum column with default 'pending'
            $table->enum('manager_status', ['pending', 'approved', 'rejected', 'accepted', 'declined'])
                  ->default('pending')
                  ->after('sourcing_remarks');

            // Adding manager_status_by nullable column
            $table->unsignedBigInteger('manager_status_by')
                  ->nullable()
                  ->after('manager_status');

            // Adding ca_status enum column with default 'pending'
            $table->enum('ca_status', ['pending', 'approved', 'rejected', 'accepted', 'declined'])
                  ->default('pending')
                  ->after('manager_status_by');

            // Adding ca_status_by nullable column
            $table->unsignedBigInteger('ca_status_by')
                  ->nullable()
                  ->after('ca_status');

            // Optional: If you have user references, you can add foreign keys
            // Uncomment the lines below if you want to set up foreign key constraints

            // $table->foreign('manager_status_by')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('ca_status_by')->references('id')->on('users')->onDelete('set null');
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
            // Dropping the added columns
            $table->dropColumn([
                'manager_status',
                'manager_status_by',
                'ca_status',
                'ca_status_by',
            ]);

            // If you added foreign keys, you need to drop them before dropping columns
            // Uncomment the lines below if you set up foreign key constraints

            // $table->dropForeign(['manager_status_by']);
            // $table->dropForeign(['ca_status_by']);
        });
    }
}
