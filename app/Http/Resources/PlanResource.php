<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'is_active' => $this->is_active,
            'price' => $this->price,
            'signup_fee' => $this->signup_fee,
            'currency' => $this->currency,
            'trial_period' => $this->trial_period,
            'trial_interval' => $this->trial_interval,
            'trial_mode' => $this->trial_mode,
            'grace_period' => $this->grace_period,
            'grace_interval' => $this->grace_interval,
            'invoice_period' => $this->invoice_period,
            'invoice_interval' => $this->invoice_interval,
            'tier' => $this->tier,
            'features' => new PlanFeatureCollection($this->whenLoaded('features')),
            'subscriptions' => new PlanSubscriptionCollection($this->whenLoaded('subscriptions')),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
