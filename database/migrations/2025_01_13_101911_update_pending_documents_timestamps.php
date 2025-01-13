<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePendingDocumentsTimestamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::table('pending_documents', function (Blueprint $table) {
            // Modify 'created_at' to use the current timestamp by default
            $table->timestamp('created_at')
                  ->useCurrent()
                  ->change();
            
            // Modify 'updated_at' to use the current timestamp by default and update on changes
            $table->timestamp('updated_at')
                  ->useCurrent()
                  ->useCurrentOnUpdate()
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        Schema::table('pending_documents', function (Blueprint $table) {
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
