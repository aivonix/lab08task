<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    
    use HasFactory;

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
/**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Listen for the 'created' and 'deleted' events
        static::updated(function ($parking) {
            $parking->updateParkingLotEmptySlots();
        });
    }

    /**
     * Update the empty_slots count in the related ParkingLot model.
     */
    protected function updateParkingLotEmptySlots()
    {
        $parkingLot = $this->parkingLot;
        if ($parkingLot) {
            $parkingLot->empty_slots = $parkingLot->calculateEmptySlots();
            $parkingLot->save();
        }
    }

}