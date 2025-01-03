<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Adding new columns
            $table->string('username')->after('id')->unique();
            $table->unsignedBigInteger('role_id')->after('remember_token');
            $table->unsignedBigInteger('entity_id')->nullable()->after('role_id');
            $table->decimal('lat', 10, 7)->nullable()->after('entity_id');
            $table->decimal('long', 10, 7)->nullable()->after('lat');
            $table->string('mobile')->nullable()->after('long');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->after('mobile');
            $table->text('address')->nullable()->after('gender');
            $table->string('aadhar_id')->nullable()->after('address');
            $table->string('pan_id')->nullable()->after('aadhar_id');
            $table->string('blood_group')->nullable()->after('pan_id');
            $table->string('pin_code', 10)->nullable()->after('blood_group');
            $table->unsignedBigInteger('state_id')->after('pin_code');
            $table->unsignedBigInteger('city_id')->nullable()->after('state_id');
            $table->date('date_of_birth')->nullable()->after('city_id');
            $table->enum('account_status', ['active', 'inactive', 'suspended'])->default('active')->after('date_of_birth');

            // Foreign Key Constraints
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('set null');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('restrict');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Dropping foreign keys
            $table->dropForeign(['role_id']);
            $table->dropForeign(['entity_id']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);

            // Dropping columns
            $table->dropColumn([
                'username',
                'role_id',
                'entity_id',
                'lat',
                'long',
                'mobile',
                'gender',
                'address',
                'aadhar_id',
                'pan_id',
                'blood_group',
                'pin_code',
                'state_id',
                'city_id',
                'date_of_birth',
                'account_status',
            ]);
        });
    }
}
