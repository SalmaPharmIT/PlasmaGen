<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameEntityLicenceNumberToEntityLicenseNumberInEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entities', function (Blueprint $table) {
            // Rename 'entity_licence_number' to 'entity_license_number'
            $table->renameColumn('entity_licence_number', 'entity_license_number');
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
            // Revert the column name back to 'entity_licence_number'
            $table->renameColumn('entity_license_number', 'entity_licence_number');
        });
    }
}
