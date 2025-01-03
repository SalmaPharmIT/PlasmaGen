<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntitiesDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('attachments'); // URL to the document
            $table->unsignedBigInteger('entity_id');
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities_documents');
    }
}
