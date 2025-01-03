<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTimestampsInCountriesTable extends Migration
{
    public function up()
    { 
        Schema::table('countries', function (Blueprint $table) {
            // Modify 'created_at' to use current timestamp by default
            $table->timestamp('created_at')
                  ->useCurrent()
                  ->change();
            
            // Modify 'updated_at' to use current timestamp by default and update on row update
            $table->timestamp('updated_at')
                  ->useCurrent()
                  ->useCurrentOnUpdate()
                  ->change();
        });
    }

    public function down()
    { 
        Schema::table('countries', function (Blueprint $table) {
            // Revert 'created_at' to nullable without default
            $table->timestamp('created_at')
                  ->nullable()
                  ->default(null)
                  ->change();
            
            // Revert 'updated_at' to nullable without default
            $table->timestamp('updated_at')
                  ->nullable()
                  ->default(null)
                  ->change();
        });
    }
}
