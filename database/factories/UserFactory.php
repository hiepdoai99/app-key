<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserTypesEnum;
use Illuminate\Support\Str;
use Spatie\Enum\Laravel\Faker\FakerEnumProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $this->faker->addProvider(new FakerEnumProvider($this->faker));

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'phone' => $this->faker->phoneNumber,
            'point' => mt_rand(0,10000),
            'user_type' => $this->faker->randomEnum(UserTypesEnum::class),
            'status' => mt_rand(0,1),
        ];
    }
}
