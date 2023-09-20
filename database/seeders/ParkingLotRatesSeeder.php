<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkingLot;

class ParkingLotRatesSeeder extends Seeder
{
    public function run()
    {
        // Find the "lab08" parking lot
        $parkingLot = ParkingLot::where('name', 'lab08')->first();

        if ($parkingLot) {
            $rates = [
                ['category' => 'A', 'day_rate' => 3.00, 'night_rate' => 2.00],
                ['category' => 'B', 'day_rate' => 6.00, 'night_rate' => 4.00],
                ['category' => 'C', 'day_rate' => 12.00, 'night_rate' => 8.00],
            ];

            foreach ($rates as $rate) {
                $parkingLot->parkingLotRates()->create([
                    'category' => $rate['category'],
                    'day_rate' => $rate['day_rate'],
                    'night_rate' => $rate['night_rate'],
                ]);
            }
        } else {
            echo "The 'lab08' parking lot was not found in the database.";
        }
    }
}