<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BuildingTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_types', function (Blueprint $table) {
            $table->uuid('id')->default(DB::raw('(UUID())'))->primary();
            $table->string('name')->default('')->unique();
            $table->string('title')->default('');
            $table->tinyInteger('order')->default(0);
            $table->string('icon')->nullable()->default(null);
            $table->tinyInteger('status')->default(1)->index('status');
            $table->timestamps();

            $table->uuid('creator_id')->nullable()->default(null);
            $table->uuid('updater_id')->nullable()->default(null);

            $table->softDeletes();

            $table->index(['status', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('building_types');
    }
}
