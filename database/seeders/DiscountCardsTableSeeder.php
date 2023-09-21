<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DiscountCardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('discount_cards')->insert([
                'code' => Str::random(12), // Generate a random 12-character code
                'discount_id' => rand(2, 4), // Generate a random value between 1 and 3
                'created_at' => now(),
            ]);
        }
    }
}
