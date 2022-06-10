<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('buildings')->delete();

        \DB::table('buildings')->insert(array (
            0 =>
            array (
                'city_id' => 'd953aaea-ae3a-11ec-b11d-0242ac120006',
                'coords' => DB::raw('POINT(55.75396, 37.620393)'),
                'created_at' => '2022-03-28 13:06:08',
                'creator_id' => NULL,
                'deleted_at' => NULL,
                'description' => 'очень крутая квартира. еще вчера была наркоманским притоном. но всех нариков забрали при облаве и мы решили ее сдать вам. ',
                'id' => 1,
                'profile_id' => 'a4b5ef8b-ae30-11ec-b11d-0242ac120006',
                'status' => 1,
                'title' => 'Крутая хатка бобра',
                'type_id' => '19409e73-ae2c-11ec-b11d-0242ac120006',
                'updated_at' => '2022-03-28 13:06:08',
                'updater_id' => NULL,
                'url' => 'test-url',
            ),
        ));

        $data = Building::query()->first()->toArray();
        $res = collect([]);
        $title = $data['title'];
        unset($data['id']);
        for ($i = 1; $i <= 30; $i++) {
            // $data['id'] = $i;
            $data['url'] = 'test-url-' . $i;
            $data['title'] = $title . ' ' . $i;
//            $data['price'] = [
//                'type'     => 'fixed',
//                'value'    => 1500 * rand(1, 15),
//                'currency' => 'RUB'
//            ];
            $data['status'] = rand(0, 4);
            //$data['address'] = 'Россия,г. Сочи, ул. Депутатская 9\1';
//            $data['rating'] = [
//                'value'  => 3.5,
//                'detail' => [],
//            ];
//            $data['reviews'] = [
//                'count' => rand(0, 1000),
//                'list'  => [],
//            ];
            // $res->push($data);
            Building::query()->create($data);
        }



    }
}
