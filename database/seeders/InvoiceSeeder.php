<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prev = 0;
        $product = Product::query()->inRandomOrder()->first();
        $plan = $product->plans->random();
        Invoice::factory(200)->for($product)->for($plan)
            ->state(function () use (&$prev){
                return ['code' => ++$prev];
            })->create();
    }
}
