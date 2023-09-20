<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Parking
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $parking_id
 * @property \Illuminate\Support\Carbon $entry_time
 * @property \Illuminate\Support\Carbon|null $exit_time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property Vehicle $vehicle
 * @property ParkingLot $parkingLot
 *
 * @package App\Models
 */
class Parking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entry_time',
        'exit_time',
        'vehicle_id',
        'parking_id',
    ];

    /**
     * Get the vehicle associated with the parking.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Get the parking lot associated with the parking.
     */
    public function parkingLot()
    {
        return $this->belongsTo(ParkingLot::class, 'parking_lot_id');
    }
}