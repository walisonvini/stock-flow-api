<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            'sku' => $this->faker->unique()->regexify('[A-Z]{3}-[0-9]{3}-[A-Z]{2}'),
            'name' => $this->faker->name(),
            'price' => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 1000),
            'description' => $this->faker->text($maxNbChars = 255),
            'category' => $this->faker->word(),
        ];
    }
}
