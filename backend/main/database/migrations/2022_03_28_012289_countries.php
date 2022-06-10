<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Countries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('code')->unique();
            $table->string('alfa2', 2)->unique();
            $table->string('alfa3', 3)->unique();
            $table->string('name')->unique();
            $table->string('title')->unique();
            $table->tinyInteger('status')->default(0)->index('status');
            $table->uuid('creator_id')->nullable()->default(null);
            $table->uuid('updater_id')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('countries');
    }
}
