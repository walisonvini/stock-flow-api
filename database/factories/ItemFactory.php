<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    
    public function definition()
    {
        return [
            'identifier' => $this->faker->uuid,
            'expiration_date' => $this->faker->date('Y-m-d', '+1 year'),
            'shelf' => $this->faker->randomLetter,
            'aisle' => $this->faker->randomDigit(2),
            'level' => $this->faker->randomDigit,
            'condition' => $this->faker->randomElement(['new', 'used', 'refurbished']),
            'status' => $this->faker->randomElement(['available', 'unavailable']),
        ];
    }
}
