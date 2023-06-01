<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->name;
        return [
            'tag' => Str::slug($name),
            'name' => $name,
            'description' => $this->faker->sentence,
            'is_active' => mt_rand(0, 1),
            'price' => 100000,
            'currency' => 'VND',
            'trial_period' => 30,
            'trial_interval' => 'day',
            'trial_mode' => 'inside',
            'invoice_period' => $this->faker->randomElement([30, 90, 180, 360, 720, 36000]),
            'invoice_interval' => 'day',
            'tier' => 1,
        ];
    }
}
