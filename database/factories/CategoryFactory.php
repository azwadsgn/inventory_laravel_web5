<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Sembako', 'Minuman', 'Bumbu Dapur', 'Kebutuhan Mandi', 'Makanan Ringan']),
        ];
    }
}