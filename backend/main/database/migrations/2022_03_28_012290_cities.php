<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Cities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('country_id')->index();
            $table->uuid('fias_id')->index()->nullable()->default(null);
            $table->integer('osm_id')->index()->nullable()->default(null);
            $table->string('title');
            $table->string('url')->default(DB::raw("(CONCAT(id))"))->unique();;
            $table->point('coords')->spatialIndex();
            $table->integer('zoom')->default(10);
            $table->integer('weight')->default(0);
            $table->tinyInteger('status')->default(0)->index('status');

            $table->uuid('creator_id')->nullable()->default(null);
            $table->uuid('updater_id')->nullable()->default(null);

            $table->timestamps();

            $table->index(['status', 'country_id', 'weight']);

            $table->foreign('country_id')->on('countries')->references('id')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cities');
    }
}
