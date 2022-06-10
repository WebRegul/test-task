<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('user_contacts_type_id');
            $table->string('data')->index('user_contacts_data');
            $table->timestamps();

            $table->foreign('user_id', 'user_contacts_user_id')
                ->references('id')
                ->on('users');

            $table->foreign('user_contacts_type_id', 'user_contacts_user_contacts_type_id')
                ->references('id')
                ->on('user_contacts_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_contacts');
    }
}
