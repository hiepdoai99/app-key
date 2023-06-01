<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use App\Enums\UserTypesEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(
            [
                'first_name' => 'Hưng',
                'last_name' => 'Trịnh Quang',
                'email' => 'hungtq@phanmemmkt.vn',
                'email_verified_at' => now(),
                'password' => Hash::make('123qweasd'),
                'remember_token' => Str::random(10),
                'user_type' => (string)UserTypesEnum::member(),
                'status' => 1,
                'phone' => '0838404568',
            ]
        );

        if (! App::environment('production')) {
            $user_type = (string)UserTypesEnum::customer();
            User::factory(200)->create([
                'user_type' => $user_type
            ]);
        }

    }
}
