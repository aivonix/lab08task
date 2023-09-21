<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Vehicle;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        // Insert the "0 car" entry
        DB::table('vehicles')->insert([
            'description' => 'Empty Parking Space',
            'plate_number' => 'EMPTY',  
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // We will create a tester car as well with the use of the static method for the plate number
        Vehicle::create([
            'description' => 'Tester Car',
            'plate_number' => Vehicle::generateRandomPlateNumber(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}