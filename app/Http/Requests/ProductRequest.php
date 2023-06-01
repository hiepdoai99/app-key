<?php

namespace App\Http\Requests;

use App\Traits\HtqRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ProductRequest extends FormRequest
{
    use HtqRequest;

    protected function getModel() : string
    {
        return 'Product';
    }

    public function prepareForValidation()
    {
        if (!$this->has('slug') && $this->has('name')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }
}
