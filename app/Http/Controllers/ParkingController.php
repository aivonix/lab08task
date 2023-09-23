<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\v1\ParkingLotController;
use App\Models\DiscountCard;
use App\Models\ParkingLot;
use App\Models\VehicleCategory;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Class ParkingController
 *
 * @package App\Http\Controllers
 */
class ParkingController extends Controller
{

    protected $parkingLotController;


    
    public function __construct(ParkingLotController $parkingLotController)
    {
        $this->parkingLotController = $parkingLotController;
    }

    /**
     * Display the current number of empty spaces in a parking lot.
     *
     * @return \Illuminate\View\View
     */
    public function showEmptySpacesForm()
    {
        $parkingLotEmptySlots = $this->parkingLotController->getEmptySpaces()->getData();
        return view('parking/empty-spaces', ['parkingLotEmptySlots' => $parkingLotEmptySlots->message->empty_spaces]);
    }

    /**
     * Display the current tax, price, and expense for a vehicle with a given plate number.
     *
     * @return \Illuminate\View\View
     */
    public function checkVehicleExpenseForm()
    {
        return view('parking/vehicle-expense');
    }

    /**
     * Show the entry form for vehicles to enter the parking lot.
     *
     * @return \Illuminate\View\View
     */
    public function showEntryForm()
    {
        $discountCards = DiscountCard::where('is_active', true)->get();
        $vehicleCategories = VehicleCategory::all();

        return view('parking/entry-form', ['discountCards' => $discountCards, 'vehicleCategories' => $vehicleCategories]);
    }
    
    /**
     * Display the exit form for vehicles leaving the parking lot.
     *
     * @return \Illuminate\View\View
     */
    public function showExitForm()
    {
        return view('parking.exit-form');
    }
    
}