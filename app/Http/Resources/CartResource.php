<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request The incoming HTTP request.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'profile_id' => $this->profile_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'profile' => $this->when($this->profile, function () {
                return [
                    'id' => $this->profile->id,
                    'user_id' => $this->profile->user_id,
                    'phone' => $this->profile->phone,
                    'address' => $this->profile->address,
                    'date_of_birth' => $this->profile->date_of_birth,
                    'created_at' => $this->profile->created_at,
                    'updated_at' => $this->profile->updated_at,
                    'user' => $this->when($this->profile->user, function () {
                        return [
                            'id' => $this->profile->user->id,
                            'name' => $this->profile->user->name,
                            'email' => $this->profile->user->email,
                            'created_at' => $this->profile->user->created_at,
                            'updated_at' => $this->profile->user->updated_at,
                        ];
                    }),
                ];
            }),
        ];
    }
}
