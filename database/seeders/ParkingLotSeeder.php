<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParkingLotSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        DB::table('parking_lots')->insert([
            'name' => 'lab08',
            'size' => '5x5',
            'capacity' => 25,
            'empty_slots' => 25,
        ]);
    }
}