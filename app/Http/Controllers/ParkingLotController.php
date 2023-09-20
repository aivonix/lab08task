<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use Illuminate\Http\Request;
use App\Models\ParkingLot;
use App\Models\Vehicle;
use DateTime;

/**
 * Class ParkingLotController
 *
 * @package App\Http\Controllers
 */
class ParkingLotController extends Controller
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

}