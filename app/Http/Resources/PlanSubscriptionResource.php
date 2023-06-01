<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class PlanSubscriptionResource extends JsonResource
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
            'tag' => $this->tag,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'is_old' => $this->is_old,
            'remaing_day' => $this->remaing_day ?? 0,
            'currency' => $this->currency,
            'subscriber_type' => $this->subscriber_type,
            'trial_period' => $this->trial_period,
            'trial_interval' => $this->trial_interval,
            'grace_period' => $this->grace_period,
            'grace_interval' => $this->grace_interval,
            'invoice_period' => $this->invoice_period,
            'invoice_interval' => $this->invoice_interval,
            'tier' => $this->tier,
            'trial_ends_at' => $this->trial_ends_at,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'cancels_at' => $this->cancels_at,
            'canceled_at' => $this->canceled_at,
            'license' => $this->license,
            'his' => $this->his,
            'online' => $this->online,
            'updated_at' => $this->updated_at->diffForHumans(),
            'subscriber' => new UserResource($this->whenLoaded('subscriber')),
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),
        ];
    }
}
