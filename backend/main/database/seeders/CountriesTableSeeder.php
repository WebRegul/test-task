<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('countries')->delete();
        
        \DB::table('countries')->insert(array (
            0 => 
            array (
                'alfa2' => 'RU',
                'alfa3' => 'RUS',
                'code' => 643,
                'created_at' => '2022-03-28 04:41:42',
                'id' => '3813c00f-ae38-11ec-b11d-0242ac120006',
                'name' => 'Российская Федерация',
                'status' => 1,
                'title' => 'Россия',
                'updated_at' => '2022-03-28 04:41:42',
            ),
            1 => 
            array (
                'alfa2' => 'BY',
                'alfa3' => 'BLR',
                'code' => 112,
                'created_at' => '2022-03-28 04:42:38',
                'id' => '5983c3f3-ae38-11ec-b11d-0242ac120006',
                'name' => 'Республика Беларусь',
                'status' => 1,
                'title' => 'Беларусь',
                'updated_at' => '2022-03-28 04:42:38',
            ),
        ));
        
        
    }
}