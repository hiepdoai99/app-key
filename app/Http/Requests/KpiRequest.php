<?php

namespace App\Http\Requests;

use App\Traits\HtqRequest;
use Illuminate\Foundation\Http\FormRequest;

class KpiRequest extends FormRequest
{
    use HtqRequest;

    public function getModel() : string
    {
        return 'Kpi';
    }

    // public function prepareForValidation()
    // {
    //     if (!$this->has('code')) {
    //         $this->merge([
    //             'code' => $this->user_id.'-'.$this->start_at.'_'.$this->end_at,
    //         ]);
    //     }
    // }
}
