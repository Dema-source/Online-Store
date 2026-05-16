<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'profile' => $this->when($this->profile, function () {
                return [
                    'phone' => $this->profile->phone,
                    'address' => $this->profile->address,
                    'date_of_birth' => $this->profile->date_of_birth,
                ];
            }),
        ];
    }
}
