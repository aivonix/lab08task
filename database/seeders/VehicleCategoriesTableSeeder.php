<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehicle_categories')->insert([
            ['name' => 'A', 'spaces' => 1],
            ['name' => 'B', 'spaces' => 2],
            ['name' => 'C', 'spaces' => 4],
        ]);
    }
}
