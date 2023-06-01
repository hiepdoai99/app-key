<?php

namespace Database\Seeders;

use App\Enums\UserTypesEnum;
use App\Models\Branch;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_type = (string)UserTypesEnum::member();
        collect(['Hà Nội', 'Hồ Chí Minh'])
            ->each(function ($name) use ($user_type){
                Branch::factory()
                    ->has(Team::factory(10)
                        ->has(User::factory()->count(mt_rand(3,5))
                                ->state(function (array $attributes, Team $team) use ($user_type) {
                                    return ['user_type' => $user_type, 'branch_id' => $team->branch->id];
                                }))
                    )
                    ->create([
                        'name' => $name,
                        'slug' => Str::slug($name)
                    ]);
            })
            ;
    }
}
