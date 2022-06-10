<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cities')->delete();
        
        \DB::table('cities')->insert(array (
            0 => 
            array (
                'coords' => '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . 'ÎÈ{0„óJ@/¨o™Ó;@',
                'country_id' => '5983c3f3-ae38-11ec-b11d-0242ac120006',
                'created_at' => '2022-03-28 05:02:41',
                'fias_id' => NULL,
                'id' => '267091bb-ae3b-11ec-b11d-0242ac120006',
                'osm_id' => 59195,
                'status' => 1,
                'title' => 'Ð³ ÐœÐ¸Ð½ÑÐº',
                'updated_at' => '2022-03-28 05:02:41',
                'url' => '267091bb-ae3b-11ec-b11d-0242ac120006',
                'weight' => 0,
            ),
            1 => 
            array (
                'coords' => '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . 'ÿ>ãÂàK@kÕ®	iÏB@',
                'country_id' => '3813c00f-ae38-11ec-b11d-0242ac120006',
                'created_at' => '2022-03-28 05:00:31',
                'fias_id' => '0c5b2444-70a0-4932-980c-b4dc0d3f02b5',
                'id' => 'd953aaea-ae3a-11ec-b11d-0242ac120006',
                'osm_id' => NULL,
                'status' => 1,
                'title' => 'Ð³ ÐœÐ¾ÑÐºÐ²Ð°',
                'updated_at' => '2022-03-28 05:00:31',
                'url' => 'd953aaea-ae3a-11ec-b11d-0242ac120006',
                'weight' => 100,
            ),
            2 => 
            array (
                'coords' => '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . '' . "\0" . 'O¬Så{œG@`<ƒ†þÛC@',
                'country_id' => '3813c00f-ae38-11ec-b11d-0242ac120006',
                'created_at' => '2022-03-28 04:58:16',
                'fias_id' => 'c1cfe4b9-f7c2-423c-abfa-6ed1c05a15c5',
                'id' => 'ebaeb615-ae3b-11ec-b11d-0242ac120006',
                'osm_id' => NULL,
                'status' => 1,
                'title' => 'Ð³ Ð Ð¾ÑÑ‚Ð¾Ð²-Ð½Ð°-Ð”Ð¾Ð½Ñƒ',
                'updated_at' => '2022-03-28 04:58:16',
                'url' => 'ebaeb615-ae3b-11ec-b11d-0242ac120006',
                'weight' => 1,
            ),
        ));
        
        
    }
}