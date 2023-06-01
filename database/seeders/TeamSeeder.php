<?php

namespace Database\Seeders;

use App\Enums\UserTypesEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $user_type = (string)UserTypesEnum::member();
        // Team::factory(20)
        //     ->has(User::factory()->count(mt_rand(3,5))
        //             ->state(function (array $attributes, Team $team) use ($user_type) {
        //                 return ['user_type' => $user_type];
        //             }))
        //     ->create();
    }
}
