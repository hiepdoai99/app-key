<?php

namespace Database\Factories;

use App\Enums\InvoiceStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Enum\Laravel\Faker\FakerEnumProvider;

class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $this->faker->addProvider(new FakerEnumProvider($this->faker));

        $member = User::query()->member()->inRandomOrder()->first();
        $customer = User::query()->customer()->inRandomOrder()->first();
        return [
            'user_id' => $member->id,
            'subscriber_id' => $customer->id,
            'total' => mt_rand(100000, 10000000),
            'code' => mt_rand(1000, 50000),
            'status' => $this->faker->randomEnum(InvoiceStatusEnum::class),
            'tax' => 0,

            'transaction' => $this->faker->randomElement(['', Str::random(7)]),
            'coupon' => $this->faker->randomElement(['', Str::random(7)]),
            'discount' => $this->faker->randomElement([0, 0.05, 0.1, 0.15]),
            'his' => Str::random(16),
        ];
    }
}
