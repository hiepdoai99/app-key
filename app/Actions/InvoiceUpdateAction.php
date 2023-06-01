<?php

namespace App\Actions;

use App\Models\Invoice;
use App\Traits\Files\FileInvoice;
use Illuminate\Support\Facades\DB;
use Spatie\QueueableAction\QueueableAction;

class InvoiceUpdateAction
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
     * @return mixed
     */
    public function execute(Invoice $invoice, array $invoiceData)
    {
        DB::beginTransaction();
        try {
            $invoice->fill($invoiceData);
            if($invoice->isDirty('his')){
                $invoice->planSubscription?->fill([
                    'his' => $invoiceData['his']
                ])->save();
            }
            if ($invoice->save()) {

                $this->handleFileInvoice($invoice);

                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return $invoice;
    }
}
