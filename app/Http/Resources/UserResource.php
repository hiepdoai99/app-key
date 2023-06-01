<?php

namespace App\Http\Resources;

use App\Models\Branch;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'user_type' => $this->user_type,
            'phone' => $this->phone,
            'point' => $this->point,
            'team_id' => $this->team_id,
            'branch_id' => $this->branch_id,
            'creator_id' => $this->creator_id,
            'status' => $this->status,
            'referral' => $this->referral,
            'code' => $this->code,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'ranges' => $this->whenAppended('ranges'),
            'revenue' => $this->whenAppended('revenue'),
            'revenue_approve' => $this->whenAppended('revenue_approve'),
            'revenue_last_month' => $this->whenAppended('revenue_last_month'),
            'memberInvoices' => InvoiceResource::collection($this->whenLoaded('memberInvoices')),
            'customerInvoices' => InvoiceResource::collection($this->whenLoaded('customerInvoices')),
            'subscribers' => UserResource::collection($this->whenLoaded('subscribers')),
            'sales' => UserResource::collection($this->whenLoaded('sales')),
            'team' => new TeamResource($this->whenLoaded('team')),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'kpis' => KpiResource::collection($this->whenLoaded('kpis')),
        ];
    }
}
