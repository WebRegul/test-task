<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Buildings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->uuid('profile_id')->index();
            $table->uuid('type_id');
            $table->uuid('city_id')->default(null);
            $table->string('url', 255)->default(DB::raw("(MD5(CONCAT(title, UNIX_TIMESTAMP())))"))->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->point('coords')->spatialIndex(); //->default(DB::raw("POINT(0, 90)"));

            $table->tinyInteger('status')->default(0)->index('status');

            $table->uuid('creator_id')->nullable()->default(null);
            $table->uuid('updater_id')->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'city_id', 'type_id']);

            $table->foreign('type_id')->on('building_types')->references('id')->onDelete('restrict');
            $table->foreign('profile_id')->on('profiles')->references('id')->onDelete('restrict');
            $table->foreign('city_id')->on('cities')->references('id')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('buildings');
    }
}
