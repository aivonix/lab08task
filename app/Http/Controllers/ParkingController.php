<?php

namespace App\Http\Controllers;

use App\Models\DiscountCard;
use App\Models\Parking;
use Illuminate\Http\Request;
use App\Models\ParkingLot;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class ParkingController
 *
 * @package App\Http\Controllers
 */
class ParkingController extends Controller
{
    /**
     * Display the current number of empty spaces in a parking lot.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function getEmptySpaces(Request $request)
    {
        $parkingLot = ParkingLot::find(1); // Replace 1 with the actual parking lot ID

        return view('empty-spaces', ['parkingLot' => $parkingLot]);
    }

    /**
     * Display the current tax, price, and expense for a vehicle with a given plate number.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function checkVehicleExpense(Request $request)
    {
        return view('vehicle-expense');
    }

    public function showEntryForm()
    {
        $discountCards = DiscountCard::where('is_active', true)->get();
        $vehicleCategories = VehicleCategory::all();

        return view('entry-form', ['discountCards' => $discountCards, 'vehicleCategories' => $vehicleCategories]);
    }

    /**
     * Handle vehicle entry into the parking lot.
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
                'plate_number' => 'required|regex:/^[A-Za-z0-9]+$/',
            ]);
    
            if(!empty($request->discount_card)){ // had to have this because the _unless rules weren't working as intended
                $temp = $request->validate([
                    'discount_card' => 'regex:/^[A-Za-z0-9]+$/',
                ]);
                $validatedData['discount_card'] = $temp['discount_card'];
            }
    
            $vehicle = Vehicle::firstOrNew(['plate_number' => $validatedData['plate_number']]);
            if(!$vehicle->exists){
                $vehicle->discount_id = 1;  // default 0 percentage discount
                $vehicle->category_id = $validatedData['vehicle_category_id'];
            }
            $vehicle->save();
            
            if(!empty($request->discount_card)){
                $discountCard = DiscountCard::where('code', $validatedData['discount_card'])->first();
    
                if ($discountCard && $discountCard->is_active) {
                    $discountCard->is_active = false;
                    $discountCard->save();
                    $vehicle->discount_id = $discountCard->id;
                    $vehicle->save();
                } else {
                    throw ValidationException::withMessages(['discount_card' => ['Invalid discount card.']]);
                }
            }
            
            // Get the vehicle size based on category
            $vehicleCategory = VehicleCategory::find($vehicle['category_id']);
            $vehicleSize = $vehicleCategory->spaces;
    
            $parkingIds = $this->checkForVehicleSpace($vehicleSize);
    
            if (count($parkingIds) > 0) {
                foreach ($parkingIds as $parkingID){
                    $parkingSlot = Parking::where('name', $parkingID)->first();
                    $parkingSlot->entry_time = now();
                    $parkingSlot->vehicle_id = $vehicle->id;
                    $parkingSlot->save();
                }
            } else {
                throw ValidationException::withMessages(['msg' => ['No room for your vehicle.']]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
    
            // Handle the error or return with errors
            return redirect()->route('homepage')->withErrors('msg', 'An error occurred: ' . $e->getMessage());
        }
        
        return redirect()->route('homepage')->with('success', 'Vehicle entry recorded successfully.');
    }


    /**
     * Check for available parking space for a vehicle.
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
            list($row, $col) = sscanf($parking, "%[A-Z]%d");

            // Check horizontally for available slots
            $horizontalSlots = [];
            for ($i = 0; $i < $vehicleSize; $i++) {
                $currentParking = chr(ord($row) + $i) . $col;
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
                $currentParking = $row . ($col + $i);
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