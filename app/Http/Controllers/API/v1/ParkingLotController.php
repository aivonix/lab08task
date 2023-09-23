<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\DiscountCard;
use App\Models\Parking;
use App\Models\ParkingLot;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use DateTime;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Parking Lot Management
 * 
 * Handle vehicle entry into the parking lot.
 *
 */
class ParkingLotController extends Controller
{
    private $emtpy_vehicle = 1;
    private $default_lab08_parking_lot = 1;
    private $default_discount = 1;

    /**
     * Handle vehicle entry into the parking lot.
     * 
     * Enter the vehicles and validate their data
     * 
     * @queryParam vehicle_category_id int required Vehicle Category ID. Example: 27
     * @queryParam plate_number string required Plate number of the vehicle. Example: NNNNN23131
     * @queryParam discount_card string Discount card code.
     * 
     *  @response {
     *     "success": true,
     *     "data": {
     *         "message": "Vehicle entry recorded successfully."
     *     }
     * }
     * @throws \Exception If there is an error.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enterParking(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $validatedData = $request->validate([
                'vehicle_category_id' => 'required|numeric',
                'plate_number' => 'required|string|size:10',
            ]);
    
            if(!empty($request->discount_card)){ // had to have this because the _unless validation rules weren't working as intended
                $temp = $request->validate([
                    'discount_card' => 'string|size:12',
                ]);
                $validatedData['discount_card'] = $temp['discount_card'];
            }
            $vehicle = Vehicle::firstOrCreate(['plate_number' => $validatedData['plate_number']]);
            if($vehicle->wasRecentlyCreated){
                $vehicle->discount_id = $this->default_discount;  // default 0 percentage discount id
                $vehicle->category_id = $validatedData['vehicle_category_id'];
            }
            $vehicle->save();
            
            if(!empty($request->discount_card)){
                $discountCard = DiscountCard::where('code', $validatedData['discount_card'])->first();
    
                if ($discountCard && $discountCard->is_active) {
                    $discountCard->is_active = false;
                    $discountCard->save();
                    $vehicle->discount_id = $discountCard->discount->id;
                    $vehicle->save();
                } else {
                    throw ValidationException::withMessages(['discount_card' => ['Invalid discount card.']]);
                }
            }
            
            // Get the vehicle size based on category
            $vehicleCategory = VehicleCategory::find($vehicle['category_id']);
            $parkingIds = $this->checkForVehicleSpace($vehicleCategory->spaces);
    
            if (count($parkingIds) > 0) {
                foreach ($parkingIds as $parkingID){
                    $parkingSlot = Parking::where('name', $parkingID)->first();
                    $parkingSlot->entry_time = now();
                    $parkingSlot->vehicle_id = $vehicle->id;
                    $parkingSlot->save();
                }
            } else {
                throw ValidationException::withMessages(['message' => ['No room for your vehicle.']]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Vehicle entry recorded successfully.',
            ],
        ]);
        // return redirect()->route('homepage')->with('message', 'Vehicle entry recorded successfully.');
    }

    /**
     * Handle vehicle exit from the parking lot.
     * 
     * Exit the vehicle from the parking lot
     * 
     * @queryParam plate_number string required Plate number of the vehicle. Example: NNNNN23131
     * 
     *  @response {
     *     "success": true,
     *     "data": {
     *         "message": 'Vehicle exit successful. Parking fee: ' . $parkingFee
     *     }
     * }
     * @throws \Exception If there is an error.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exitParking(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'plate_number' => 'required|string|size:10', 
            ]);

            $vehicle = Vehicle::where('plate_number', $validatedData['plate_number'])->first();
            
            if (!$vehicle) {
                throw ValidationException::withMessages(['message' => ['Vehicle not found.']]);
            }
            
            $parkingEntries = Parking::where('vehicle_id', $vehicle->id)->get();

            if (count($parkingEntries) <= 0) {
                throw ValidationException::withMessages(['message' => ['Your car is not in the parking.']]);
            }
            
            // calculate this before we remove the parking entries
            $parkingFee = $this->calculateParkingFee($vehicle);
            foreach ($parkingEntries as $parkingEntry) {
                $parkingEntry->vehicle_id = $this->emtpy_vehicle; 
                $parkingEntry->entry_time = null;
                $parkingEntry->save();
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Vehicle exit successful. Parking fee: ' . $parkingFee,
            ],
        ]);
    }

    /**
     * Check the parking fee for a vehicle based on its plate number.
     *
     * Show parking fee for this vehicle
     * 
     * @queryParam vehicle_number string required Plate number of the vehicle. Example: NNNNN23131
     * 
     *  @response {
     *     "success": true,
     *     "data": {
     *         "message": '12'
     *     }
     * }
     * @throws \Exception If there is an error.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkVehicleExpense(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'vehicle_number' => 'required|regex:/^[A-Z0-9]{10}$/',
            ]);

            $vehicleNumber = strtoupper($request->input('vehicle_number'));

            // Find the vehicle by its number
            $vehicle = Vehicle::where('plate_number', $vehicleNumber)->first();
            if ($vehicle) {
                $fee = $this->calculateParkingFee($vehicle);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'message' => $fee,
                    ],
                ]);
            } else {
                // Vehicle not found
                throw ValidationException::withMessages(['vehicle_number' => 'Vehicle not found']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Return the number of empty spaces in a parking lot.
     *
     * Show empty spaces
     * 
     *  @response {
     *     "success": true,
     *     "message": {
     *         "empty_spaces": 10
     *     }
     * }
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmptySpaces(): JsonResponse
    {
        $parkingLot = ParkingLot::find($this->default_lab08_parking_lot); // Replace 1 with the actual parking lot ID

        return response()->json([
            'success' => true,
            'message' => [
                'empty_spaces' => $parkingLot->empty_slots,
            ],
        ]);

    }




    /**
     * Calculate the parking fee for a vehicle.
     *
     * @param Vehicle $vehicle The vehicle for which to calculate the fee.
     * @return float The calculated parking fee.
     */
    public function calculateParkingFee(Vehicle $vehicle)
    {
        if (count($vehicle->parking) <= 0) {
            throw new \Exception('Your vehicle does not have a registered slot with this parking. Thank you for being a recurring customer!');
        }
        $entryTime = new DateTime($vehicle->parking->first()->entry_time);
        $currentTime = new DateTime();

        // Calculate the total time difference in hours
        $totalHourDifference = $entryTime->diff($currentTime)->h + $entryTime->diff($currentTime)->d * 24;

        $dayRate = 0;
        $nightRate = 0;

        // Retrieve the parking lot rates for the specific vehicle category
        $parkingLotRates = $vehicle->parking->first()->parkingLot->parkingLotRates->find($vehicle->category->id);

        // Calculate the total fee based on the time difference
        for ($i = 0; $i <= $totalHourDifference; $i++) {
            // Determine if the current time is in the day or night interval
            $isDayRate = ($entryTime->format('H') >= '12' && $entryTime->format('H') < '24');

            if ($isDayRate) {
                $dayRate += $parkingLotRates->day_rate;
            } else {
                $nightRate += $parkingLotRates->night_rate;
            }

            // Increment entry time by 1 hour for the next iteration
            $entryTime->modify('+1 hour');
        }

        $totalFee = $dayRate + $nightRate;

        // Apply the discount if applicable
        $discountPercentage = $vehicle->discount->percentage;

        if ($discountPercentage > 0) {
            $totalFee = $totalFee * (1 - ($discountPercentage / 100));
        }

        return round($totalFee, 2); // Round the fee to 2 decimal places
    }

    /**
     * Check for available parking space for a vehicle. Lazy approach.
     *
     * @param int $vehicleSize
     * @return array
     */
    private function checkForVehicleSpace($vehicleSize)
    {
        // Retrieve all parking slots where entry_time is null
        $availableParkings = Parking::whereNull('entry_time')->pluck('name')->toArray();

        foreach ($availableParkings as $parking) {
            // Split the parking name into row and column (e.g., "A1" => ["A", 1])
            list($letters, $digits) = sscanf($parking, "%[A-Z]%d");

            // Check horizontally for available slots
            $horizontalSlots = [];
            for ($i = 0; $i < $vehicleSize; $i++) {
                // TODO: In case of bigger parking lot sizes, and different naming convetion e.g. AA AB etc, this and generateLetterParkingName() need to be reworked, leaving it for now.
                $currentParking = chr(ord($letters)+ $i) . $digits;
                if (in_array($currentParking, $availableParkings)) {
                    $horizontalSlots[] = $currentParking;
                }
            }

            if (count($horizontalSlots) === $vehicleSize) {
                return $horizontalSlots;
            }

            // Check vertically for available slots
            $verticalSlots = [];
            for ($i = 0; $i < $vehicleSize; $i++) {
                $currentParking = $letters . ($digits + $i);
                if (in_array($currentParking, $availableParkings)) {
                    $verticalSlots[] = $currentParking;
                }
            }

            if (count($verticalSlots) === $vehicleSize) {
                return $verticalSlots;
            }
        }

        return [];
    }

}