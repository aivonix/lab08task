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
            'plate_number' => 'EMPTY',  //this allows us to insert a default plate number for default car. When using the create method, however, it will generate a nameplate based on the logic in the Vehicle Model 
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // This is the actual use of the create method from the Vehicle Model. We will create a tester car as well with this seed.
        Vehicle::create([
            'description' => 'Tester Car',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}