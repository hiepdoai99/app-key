<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Auth::login(User::where('email', 'hungtq@phanmemmkt.vn')->first());
        $invoices = Invoice::with(['subscriber', 'plan', 'user'])->get();
        $invoices->each(function(Invoice $invoice) {
            $product = $invoice->product;
            $license = $product->prefix_key . $invoice->his;
            $sub = $invoice->subscriber->newSubscription($license, $invoice, $invoice->plan, $product);
            $invoice->plan_subscription_id = $sub->id;
            $invoice->save();
        });
    }
}
