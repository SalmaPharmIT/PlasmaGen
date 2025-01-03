<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTimestampsInCitiesTable extends Migration
{
    public function up()
    { 
        Schema::table('cities', function (Blueprint $table) {
            $table->timestamp('created_at')
                  ->useCurrent()
                  ->change();
            
            $table->timestamp('updated_at')
                  ->useCurrent()
                  ->useCurrentOnUpdate()
                  ->change();
        });
    }

    public function down()
    { 
        Schema::table('cities', function (Blueprint $table) {
            $table->timestamp('created_at')
                  ->nullable()
                  ->default(null)
                  ->change();
            
            $table->timestamp('updated_at')
                  ->nullable()
                  ->default(null)
                  ->change();
        });
    }
}
