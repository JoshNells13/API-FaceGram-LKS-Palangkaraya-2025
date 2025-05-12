<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return[
            'id' => $this->follower->id,
            'full_name' => $this->follower->full_name,
            'username' => $this->follower->username,
            'bio' => $this->follower->bio,
            'is_private' => $this->follower->is_private,
            'created_at' => $this->follower->created_at,
            'follower_status' => $this->follower ? 'follower' : 'requested'
        ];
    }
}