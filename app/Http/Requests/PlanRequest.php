<?php

namespace App\Http\Requests;

use App\Traits\HtqRequest;
use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
{
    use HtqRequest;

    protected function getModel() : string
    {
        return 'Plan';
    }

    public function prepareForValidation()
    {
        if (is_null($this->trial_period)) {
            $this->merge([
                'trial_period' => 0,
            ]);
        }
        if (is_null($this->price)) {
            $this->merge([
                'price' => 0,
            ]);
        }
        if (is_null($this->invoice_period)) {
            $this->merge([
                'invoice_period' => 0,
            ]);
        }
    }
}
