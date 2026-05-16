<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'user_id' => $this->user_id,
            'phone' => $this->phone,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->when(auth()->check() && auth()->user()->hasRole('super_administrator') && $this->user, function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'created_at' => $this->user->created_at,
                    'updated_at' => $this->user->updated_at,
                ];
            }),
            'cart' => $this->when(auth()->check() && auth()->user()->hasRole('super_administrator') && $this->cart, function () {
                return [
                    'id' => $this->cart->id,
                    'profile_id' => $this->cart->profile_id,
                    'created_at' => $this->cart->created_at,
                    'updated_at' => $this->cart->updated_at,
                ];
            }),
        ];
    }
}
