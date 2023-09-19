<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParkingSeeder extends Seeder
{
    public function run()
    {
       // Find the "lab08" parking lot
       $parkingLot = DB::table('parking_lots')->where('name', 'lab08')->first();

       // Check if the parking lot exists
       if ($parkingLot) {
           // Generate and insert 200 parking entries
           $parkingEntries = [];

           for ($i = 1; $i <= 200; $i++) {
               $name = $this->generateParkingName($i);

               $parkingEntries[] = [
                   'name' => $name,
                   'parking_lot_id' => $parkingLot->id,
                   'created_at' => now(),
                   'updated_at' => now(),
               ];
           }

           DB::table('parkings')->insert($parkingEntries);
       } else {
           // Handle the case where the "lab08" parking lot was not found
           echo "The 'lab08' parking lot was not found in the database.";
       }
    }

    /**
     * Generate a parking name based on a given number.
     *
     * The method converts the provided number to a base-26 representation,
     * where each digit corresponds to a capital letter (A-Z).
     * For example, 1 corresponds to 'A', 2 corresponds to 'B', and so on.
     *
     * @param int $number The number to generate the parking name from.
     * @return string The generated parking name.
     */
    private function generateParkingName($number)
    {
        // Convert the number to a base-26 representation
        $digits = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($digits);
        $name = '';

        while ($number > 0) {
            $index = ($number - 1) % $base;
            $name = $digits[$index] . $name;
            $number = intval(($number - 1) / $base);
        }

        return $name;
    }
}