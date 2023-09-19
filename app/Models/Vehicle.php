<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Vehicle
 *
 * @property int $id
 * @property int $category_id
 * @property int $discount_id
 * @property string $plate_number
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property Category $category
 * @property Discount $discount
 *
 * @package App\Models
 */
class Vehicle extends Model
{
 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plate_number',
        'category_id',
        'discount_id',
    ];

    /**
     * Get the category associated with the vehicle.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the discount associated with the vehicle.
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}
