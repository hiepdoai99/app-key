<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'subscriber' => new UserResource($this->whenLoaded('subscriber')),
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'planSubscription' => new PlanSubscriptionResource($this->whenLoaded('planSubscription')),
            'bank' => new BankResource($this->whenLoaded('bank')),
            'files' => FileResource::collection($this->whenLoaded('files')),
            'total' => $this->total,
            'code' => $this->code,
            'status' => $this->status,
            'tax' => $this->tax,
            'bank_id' => $this->bank_id,
            'bank_memo' => $this->bank_memo,
            'transaction' => $this->transaction,
            'coupon' => $this->coupon,
            'discount' => $this->discount,
            'his' => $this->his,
            'reason' => $this->reason,
            'note' => $this->note,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
