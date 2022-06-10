<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'created_at' => '2022-03-28 03:45:49',
                'id' => '69e53e71-ae30-11ec-b11d-0242ac120006',
                'login' => 'ex1@webregul.ru',
                'password' => '',
                'status' => 1,
                'updated_at' => '2022-03-28 03:45:49',
                'verified_at' => '2022-03-28 03:45:49',
            ),
            1 => 
            array (
                'created_at' => '2022-03-28 03:45:49',
                'id' => '81fa76a1-ae30-11ec-b11d-0242ac120006',
                'login' => 'ex2@webregul.ru',
                'password' => '',
                'status' => 1,
                'updated_at' => '2022-03-28 03:45:49',
                'verified_at' => '2022-03-28 03:45:49',
            ),
        ));
        
        
    }
}