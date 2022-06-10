<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Profiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->default(DB::raw('(UUID())'))->primary();
            $table->uuid('user_id')->index('user')->index();
            $table->uuid('register_source_id')->nullable()->default(null);
            $table->string('url')->index('url')->default(DB::raw("(CONCAT(id))"))->unique();
            $table->string('name')->default('');
            $table->string('surname')->default('');
            $table->string('patronymic')->default('');
            $table->date('birthday_at')->nullable()->default(null);
            $table->boolean('is_hotel')->default(false);
            $table->tinyInteger('status')->default(0)->index('status');

            $table->uuid('creator_id')->nullable()->default(null);
            $table->uuid('updater_id')->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->on('users')->references('id')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('profiles');
    }
}
