<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BuildingTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('building_types')->delete();

        \DB::table('building_types')->insert(array (
            0 =>
            array (
                'created_at' => '2022-03-28 03:14:56',
                'deleted_at' => NULL,
                'id' => '19409e73-ae2c-11ec-b11d-0242ac120006',
                'name' => 'flat',
                'status' => 1,
                'title' => 'Квартира',
                'updated_at' => '2022-03-28 03:14:56',
            ),
            1 =>
            array (
                'created_at' => '2022-03-28 03:14:56',
                'deleted_at' => NULL,
                'id' => '2c032f61-ae2c-11ec-b11d-0242ac120006',
                'name' => 'room',
                'status' => 1,
                'title' => 'Комната',
                'updated_at' => '2022-03-28 03:14:56',
            ),
        ));


    }
}
