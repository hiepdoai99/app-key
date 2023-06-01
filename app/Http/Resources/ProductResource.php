<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'prefix_key' => $this->prefix_key,
            'status' => $this->status,
            'version' => $this->version,
            'created_at' => $this->created_at->format('Y-m-d'),
            'plans' => new PlanCollection($this->whenLoaded('plans')),
            'planSubscriptions' => new PlanSubscriptionCollection($this->whenLoaded('planSubscriptions')) ,
            'ranges' => $this->whenAppended('ranges'),
            'revenue' => $this->whenAppended('revenue'),
            'revenue_approve' => $this->whenAppended('revenue_approve'),
            'invoices' => new InvoiceCollection($this->whenLoaded('invoices')) ,
        ];
    }
}
