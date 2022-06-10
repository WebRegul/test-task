<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('user_id');
            $table->enum('channel', ['sms', 'email'])->default('sms');
            $table->string('code', 4);
            $table->tinyInteger('status')->unsigned()->default(0);

            $table->uuid('creator_id');
            $table->uuid('updater_id');

            $table->timestamp('sended_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();

            $table->foreign('creator_id', 'user_code_creator_id')
                ->references('id')
                ->on('users');
            $table->foreign('updater_id', 'user_code_updater_id')
                ->references('id')
                ->on('users');
            $table->foreign('user_id', 'user_code_user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_codes');
    }
}
