<?php

namespace Tests\Feature;

use App\Models\Parking;
use App\Models\Vehicle;
use Tests\TestCase;
use Illuminate\Http\Response;

class ParkingLotControllerTest extends TestCase
{

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    // use RefreshDatabase;
    public function testVehicleEntersParkingLotSuccessfully()
    {
        $this->seed();
        $vehicle = Vehicle::factory()->create();
        // Create a new vehicle
        
        $response = $this->postJson('/api/v1/enter-parking', [
            'vehicle_category_id' => $vehicle->category_id,
            'plate_number' => $vehicle->plate_number,
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'message' => 'Vehicle entry recorded successfully.',
                ],
            ]);
    }
 
    public function testVehicleEntersParkingLotWithInvalidData()
    {
        $invalidData = [
            'vehicle_category_id' => '', // Missing required field.
            'plate_number' => 'ABC123[567', // Invalid plate number format.
        ];

        $response = $this->json('POST', '/api/v1/enter-parking', $invalidData);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'message' => 'The vehicle category id field is required.',
                ],
            ]);
    }
 
    public function testVehicleExitsParkingLotSuccessfully()
    {
        $vehicle = Vehicle::factory()->create();
        // Create a new vehicle
        
        // Then take a parking slot, in our case the last one, and add the vehicle to that slot. Disregard the category size.
        $parkings = Parking::all();
        $parking = $parkings->last();
        $parking->vehicle_id = $vehicle->id;
        $parking->entry_time = now();
        $parking->save();

        // Send request to exit the vehicle
        $response = $this->json('POST', '/api/v1/exit-parking', [
            'plate_number' => $vehicle->plate_number,
        ]);

        $response->assertStatus(200);
    }
 
    public function testVehicleExitsParkingLotWithInvalidData()
    {
        $response = $this->json('POST', '/api/v1/exit-parking', [
            'plate_number' => 'INVALID123', 
        ]);

        $response->assertJson([
            'success' => false,
            'error' => [
                'message' => 'Vehicle not found.',
            ],
        ]);
    }
 
    public function testCheckVehicleExpense()
    {
        $vehicle = Vehicle::factory()->create();

        $parkings = Parking::all();
        $parking = $parkings->last();
        $parking->vehicle_id = $vehicle->id;
        $parking->entry_time = now();
        $parking->save();

        $response = $this->json('POST', '/api/v1/check-vehicle-expense', [
            'vehicle_number' => $vehicle->plate_number, 
        ]);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'message',
            ],
        ]);
    }
 
    public function testCheckVehicleExpenseWithInvalidData()
    {
        $response = $this->json('POST', '/api/v1/check-vehicle-expense', ['vehicle_number' => 'loco']);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }
 
    public function testGetEmptySpaces()
    {
        // This needs to be changed, was tested with 5x5 parking lot size and was 22
        $expectedEmptySpaces = 197;
        $response = $this->json('GET', '/api/v1/get-empty-spaces');

        $response->assertJson([
            'message' => [
                'empty_spaces' => $expectedEmptySpaces,
            ],
        ]);
    }
}
