<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\Parking;
use App\Models\Vehicle;
use DateTime;
use Illuminate\Http\Request;

class ParkingLotController extends Controller
{
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
                        'fee' => $fee,
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
     * Calculate the parking fee for a vehicle.
     *
     * @param Vehicle $vehicle The vehicle for which to calculate the fee.
     * @return float The calculated parking fee.
     */
    public function calculateParkingFee(Vehicle $vehicle)
    {
        $entryTime = new DateTime($vehicle->parking->first()->entry_time);

        // Get the current time
        $currentTime = new DateTime();

        // Calculate the time difference in hours
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

        // Calculate the total fee
        $totalFee = $dayRate + $nightRate;

        // Apply the discount if applicable
        $discountPercentage = $vehicle->discount->percentage ?? 0;

        if ($discountPercentage > 0) {
            $totalFee = $totalFee * (1 - ($discountPercentage / 100));
        }

        return round($totalFee, 2); // Round the fee to 2 decimal places
    }

}