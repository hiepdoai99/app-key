<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'account_holder' => $this->account_holder,
            'account_number' => $this->account_number,
            'name_bank' => $this->name_bank,
            'short_name' => $this->short_name,
            'code' => $this->code,
            'branch' => $this->branch,
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),

        ];
    }
}
