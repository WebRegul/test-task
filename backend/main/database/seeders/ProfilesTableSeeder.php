<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProfilesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('profiles')->delete();

        \DB::table('profiles')->insert(array (
            0 =>
            array (
                'birthday_at' => NULL,
                'created_at' => '2022-03-28 03:47:28',
                'id' => 'a4b5ef8b-ae30-11ec-b11d-0242ac120006',
                'is_hotel' => 0,
                'name' => 'Зигмунд',
                'patronymic' => 'Альбертович',
                'status' => 1,
                'surname' => 'Фрейд',
                'updated_at' => '2022-03-28 03:47:28',
                'user_id' => '69e53e71-ae30-11ec-b11d-0242ac120006',
            ),
            1 =>
            array (
                'birthday_at' => NULL,
                'created_at' => '2022-03-28 03:48:16',
                'id' => 'c0eee9b4-ae30-11ec-b11d-0242ac120006',
                'is_hotel' => 0,
                'name' => 'Кальцифер',
                'patronymic' => 'Люциферович',
                'status' => 1,
                'surname' => 'Огненный',
                'updated_at' => '2022-03-28 03:48:16',
                'user_id' => '81fa76a1-ae30-11ec-b11d-0242ac120006',
            ),
        ));


    }
}
