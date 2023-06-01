<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'branch_id' => $this->branch_id,
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'parentTeam' => new TeamResource($this->whenLoaded('parentTeam')),
            'childTeams' => TeamResource::collection($this->whenLoaded('childTeams')),
            'kpis' => KpiResource::collection($this->whenLoaded('kpis')),
            'ranges' => $this->whenAppended('ranges'),
            'revenue' => $this->whenAppended('revenue'),
            'revenue_approve' => $this->whenAppended('revenue_approve'),
        ];
    }
}
