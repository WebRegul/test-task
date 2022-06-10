<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(ProfilesTableSeeder::class);

        $this->call(CountriesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);

        $this->call(BuildingTypesTableSeeder::class);

        $this->call(BuildingsTableSeeder::class);
    }
}
