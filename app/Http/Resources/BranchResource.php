<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'teams' => TeamResource::collection($this->whenLoaded('teams')),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'kpis' => KpiResource::collection($this->whenLoaded('kpis')),
            'ranges' => $this->whenAppended('ranges'),
            'revenue' => $this->whenAppended('revenue'),
            'revenue_approve' => $this->whenAppended('revenue_approve'),
            'revenue_today' => $this->whenAppended('revenue_today'),
        ];
    }
}
