<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerRequestItemResource extends JsonResource
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
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at->format('d.m.Y H:i'),
            $this->mergeWhen($this->status === 'Resolve', [
                'comment' => $this->comment,
            ]),
        ];
    }
}
