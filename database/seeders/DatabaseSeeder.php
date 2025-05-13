<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;

use Database\Seeders\StatesTableSeeder as SeedersStatesTableSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(CountriesTableSeeder::class);
        $this->call(SeedersStatesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        Department::create([
            'name' => 'React Js',

        ]);
    }
}
