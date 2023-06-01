<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $subject_type = __NAMESPACE__.'\\'.collect(explode('\\', $this->subject_type))->last().'Resource';
        $causer_type = __NAMESPACE__.'\\'.collect(explode('\\', $this->causer_type))->last().'Resource';
        $causer = method_exists($causer_type, 'toArray') ? new $causer_type($this->whenLoaded('causer')) : null;
        $subject = method_exists($subject_type, 'toArray') ? new $subject_type($this->whenLoaded('subject')) : null;

        return [
            'id' => $this->id,
            'event' => $this->mapperEvent(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            // 'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'subject_type' => $this->mapperSubject(),
            'subject' => $subject,
            'causer' => $causer,
            'changes' => $this->changes,
            'description' => $this->mapperDescription($causer, $subject),
            'list_subject' => $this->listSubject(),
        ];
    }

    private function mapperEvent()
    {
        $map = [
            'created' => 'Thêm mới',
            'updated' => 'Chỉnh sửa',
            'deleted' => 'Xóa',
        ];
        return $map[$this->event];
    }

    private function mapperSubject()
    {
        $map = $this->listSubject();
        return $map[$this->subject_type];
    }

    private function listSubject()
    {
        return [
            'App\Models\User' => 'Quản lý User',
            'App\Models\Invoice' => 'Hóa đơn',
            'App\Models\File' => 'File',
        ];
    }

    private function mapperDescription($causer, $subject) : string
    {
        $name = !empty($causer) ? $causer->name : 'Hệ thống';
        $des = $this->mapperEvent().': ' .$this->mapperSubject() .' - bởi '. $name;

        if ($this->changes->has('attributes')) {
            $des .= '. Giá trị mới: ';
            $des .= collect($this->changes->all()['attributes'])->join(', ');
        }
        if ($this->changes->has('old')) {
            $des .= '. Giá trị cũ: ';
            $des .= collect($this->changes->all()['old'])->except(['updated_at'])->join(', ');
        }

        return $des;
    }
}
