<?php


namespace App\Traits\Files;


use App\Models\Invoice;

trait FileInvoice
{
    use FileHandler;

    public function handleFileInvoice(Invoice $invoice)
    {
        if ($file = request()->file('upload_invoice')){
            $path = $this->uploadInvoiceImage($file);
            $invoice->files()->create([
                'type' => 'file_invoice',
                'path' => $path,
            ]);
        }
        return $this;
    }

}
