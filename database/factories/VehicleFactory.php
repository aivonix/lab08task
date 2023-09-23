<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plate = strtoupper(Str::random(10));
        return [
            'category_id' => rand(1,3),
            'discount_id' => rand(2,4),
            'plate_number' => $plate,
            'description' => "Automatic tester for ".$plate,
        ];
    }
}
