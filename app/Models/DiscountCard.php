<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DiscountCard
 *
 * @property int $id
 * @property string $code
 * @property int $discount_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property Discount $discount
 *
 * @package App\Models
 */
class DiscountCard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'discount_id',
    ];

    /**
     * Get the discount associated with the discount card.
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}