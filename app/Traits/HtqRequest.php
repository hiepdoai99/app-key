<?php
declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

trait HtqRequest
{
    abstract function getModel() : string;

    private $enumClass = '';
    private $pass = false;
    private $rules = [];
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->getEnumPermission();
        if (method_exists($this, 'prepareForAuthorize')) {
            $this->prepareForAuthorize();
        }

        $this->checkRequest();
        return $this->pass;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (method_exists($this, 'prepareForValidation')) {
            $this->prepareForValidation();
        }
        return $this->rules;
    }

    private function getEnumPermission()
    {
        $this->enumClass = '\\App\\Enums\\PermissionsEnum::'.Str::lower($this->getModel());
    }

    private function checkRequest()
    {
        $model = '\\App\\Models\\'.ucfirst($this->getModel());
        $rules = [];
        if (class_exists($model)) {
            if (method_exists($model, 'getRules')) {
                $rules = app($model)->getRules();
            }
        }
        switch (strtoupper($this->method())) {
            case 'HEAD':
            case 'GET':
                $this->pass = $this->user()->can(
                    (string)call_user_func($this->enumClass.'_read')
                );
                break;

            case 'POST':
                $this->pass = $this->user()->can(
                    (string)call_user_func($this->enumClass.'_create')
                );
                $this->rules = $rules;
                break;

            case 'PUT':
            case 'PATCH':
                $this->pass = $this->user()->can(
                    (string)call_user_func($this->enumClass.'_update')
                );
                $this->rules = $rules;
                break;

            default: // delete || other
                $this->pass = $this->user()->can(
                    (string)call_user_func($this->enumClass.'_delete')
                );
                break;
        }
        return $this;
    }
}
