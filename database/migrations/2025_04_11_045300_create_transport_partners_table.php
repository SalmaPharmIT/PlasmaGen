<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_partners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            // Place status here after name:
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('contact_person')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('alternative_contact_number')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->unsignedBigInteger('entity_id');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_partners');
    }
}
