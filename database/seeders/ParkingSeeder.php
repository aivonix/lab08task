<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Jobs\InsertParkingEntries;
use App\Models\Parking;

class ParkingSeeder extends Seeder
{
    public function run()
    {
        // Find the "lab08" parking lot
        $parkingLot = DB::table('parking_lots')->where('name', 'lab08')->first();
        $vehicle = DB::table('vehicles')->where('id', '1')->first();

        // Check if the parking lot exists
        if ($parkingLot) {
            $parkingEntries = [];
            list($maxRows, $maxCols) = explode('x', $parkingLot->size);
            // I leave this here hardcoded on purpose. 

            for ($col = 1; $col <= $maxCols; $col++) {
                for ($row = 1; $row <= $maxRows; $row++) {
                    $name = $this->generateParkingName($this->generateLetterParkingName($col), $row);

                    $parkingEntries[] = [
                        'name' => $name,
                        'parking_lot_id' => $parkingLot->id,
                        'vehicle_id' => $vehicle->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            // Idea here is to employ a queue to deal with possible large amounts of maxRows and maxCols. The connection drops, tho, still haven't fixed that issue.
            InsertParkingEntries::dispatch($parkingEntries);

            // also since we created a tester car lets allocate it to a random slot in the parking lot for testing purposes
            $xSlot = rand(1, $maxCols);
            $ySlot = rand(1, $maxRows);
            $parkingName = $this->generateParkingName($this->generateLetterParkingName($xSlot), $ySlot);
            $parkingEntry = Parking::where('name', $parkingName)->first();

            if ($parkingEntry) {
                $parkingEntry->update(['entry_time' => now()]);
            } 
        } else {
            // Handle the case where the "lab08" parking lot was not found
            echo "The 'lab08' parking lot was not found in the database.";
        }
    }

    /**
     * Generate a parking name based on column (X) and row (Y) labels.
     *
     * @param string $column The column label (e.g., 'A', 'B', 'C', ...)
     * @param int $row The row number (e.g., 1, 2, 3, ...)
     * @return string The generated parking name (e.g., 'A1', 'B2', ...)
     */
    private function generateParkingName($column, $row)
    {
        return $column . $row;
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
    private function generateLetterParkingName($number)
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