<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingLotRate extends Model
{
    use HasFactory;

    /**
     * Get the Parking Lot associated with the parking lot rates.
     */
    public function parkingLot()
    {
        return $this->belongsTo(ParkingLot::class);
    }
}
