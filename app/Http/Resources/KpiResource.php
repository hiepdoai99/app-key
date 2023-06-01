<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KpiResource extends JsonResource
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
            'code' => $this->code,
            'target' => $this->target,
            'start_at' => $this->start_at->format('Y-m-d H:i:s'),
            'end_at' => $this->end_at->format('Y-m-d H:i:s'),
            'branch_id' => $this->branch_id,
            'team_id' => $this->team_id,
            'user_id' => $this->user_id,
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'team' => new TeamResource($this->whenLoaded('team')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
