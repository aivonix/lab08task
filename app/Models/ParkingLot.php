<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ParkingLot
 *
 * @property int $id
 * @property string $name
 * @property int $capacity
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @package App\Models
 */
class ParkingLot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'capacity',
    ];

    /**
     * Get the parkings associated with the parking lot.
     */
    public function parking()
    {
        return $this->hasMany(Parking::class, 'parking_lot_id');
    }

    /**
     * Get the Rates associated with the parking lot.
     */
    public function parkingLotRates()
    {
        return $this->hasMany(ParkingLotRate::class);
    }

    /**
     * Calculate the number of empty slots in the parking lot.
     *
     * @return int
     */
    public function calculateEmptySlots()
    {
        return Parking::where('parking_lot_id', $this->id)
            ->whereNull('entry_time')
            ->count();
    }
}