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
                    $name = generateParkingLabel($col).$row;

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
            $parkingName = generateParkingLabel($xSlot).$ySlot;
            $parkingEntry = Parking::where('name', $parkingName)->first();

            if ($parkingEntry) {
                $parkingEntry->update(['entry_time' => now(), 'vehicle_id' => '2']);
            } 
        } else {
            // Handle the case where the "lab08" parking lot was not found
            echo "The 'lab08' parking lot was not found in the database.";
        }
    }

}