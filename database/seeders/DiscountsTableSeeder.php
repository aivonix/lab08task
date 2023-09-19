<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('discounts')->insert([
            ['name' => 'None', 'percentage' => 0],
            ['name' => 'Silver', 'percentage' => 10],
            ['name' => 'Gold', 'percentage' => 15],
            ['name' => 'Platinum', 'percentage' => 20],
        ]);
    }
}