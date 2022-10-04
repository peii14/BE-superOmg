<?php

namespace Database\Factories;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->word,
            'detail' => $this->faker->paragraph,
            'price' => $this->faker->numberBetween(10,100),
            'stock' => $this->faker->randomDigit,
            'discount' => $this->faker->numberBetween(2,30)
        ];
    }
}

