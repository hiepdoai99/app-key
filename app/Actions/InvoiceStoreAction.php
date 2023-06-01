<?php

namespace App\Actions;

use App\Models\Plan;
use App\Models\User;
use App\Models\Invoice;
use App\Models\PlanSubscription;
use App\Traits\Files\FileInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueueableAction\QueueableAction;

class InvoiceStoreAction
{
    use QueueableAction, FileInvoice;

    /**
     * Create a new action instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Prepare the action for execution, leveraging constructor injection.
    }

    /**
     * Execute the action.
     *
     * @return PlanSubscription | false
     */
    public function execute(array $invoiceData)
    {
        $plan = Plan::find($invoiceData['plan_id']);
        $product = $plan->product;
        $license = $product->prefix_key . $invoiceData['his'];

        $subscripted = PlanSubscription::query()->forLicense($license)->first();
        if ($subscripted) {
            return $subscripted;
        }

        $subscriber = User::find($invoiceData['subscriber_id']);
        if (Auth::hasUser() && Auth::user()->email === $subscriber->email) {
            $invoiceData['total'] = 0.00;
        }
        DB::beginTransaction();
        try {
            $invoice = new Invoice();
            if ($invoice->fill($invoiceData)->save()) {
                if ($subscriber && $plan) {
                    $subscription = $subscriber->newSubscription($license, $invoice, $plan, $product);
                    $invoice->plan_subscription_id = $subscription->id;
                    $invoice->save();

                    $this->handleFileInvoice($invoice);

                    DB::commit();
                    return $subscription;
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return false;
    }
}
