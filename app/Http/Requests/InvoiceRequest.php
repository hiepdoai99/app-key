<?php

namespace App\Http\Requests;

use App\Traits\HtqRequest;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    use HtqRequest;

    public function getModel() : string
    {
        return 'Invoice';
    }

    public function prepareForValidation()
    {
        if ($this->has('total') && is_null($this->total)) {
            $this->merge([
                'total' => 0,
            ]);
        }
        if ($this->has('price') && is_null($this->price)) {
            $this->merge([
                'price' => 0,
            ]);
        }
        if ($this->has('discount') && is_null($this->discount)) {
            $this->merge([
                'discount' => 0,
            ]);
        }
    }
}
