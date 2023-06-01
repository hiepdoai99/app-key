<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->unique()->sentence(mt_rand(2,5));
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'prefix_key' => Str::random(5),
            'description' => $this->faker->paragraph(mt_rand(3,5)),
            'status' => mt_rand(0,1),
            'version' => '1.0.0',
        ];
    }
}
