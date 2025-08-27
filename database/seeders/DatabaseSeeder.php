<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(ShopsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(VariationsTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(PagesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);

        // for dev purpose -- [TODO:: comment this for production & fresh installation] 
        // $this->call(AreasTableSeeder::class);
        // $this->call(StatesTableSeeder::class);
        // $this->call(CitiesTableSeeder::class);
        // $this->call(DevDataSeeder::class);
    }
}
