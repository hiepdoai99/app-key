<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class KpiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name',
            'code',
            'target',
            'start_at',
            'end_at',
            'branch_id',
            'team_id',
            'user_id',
        ];
    }
}
