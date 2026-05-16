<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavouriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'guest_token' => $this->guest_token,
            'product_id' => $this->product_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relations
            'user' => $this->when($this->user, function () {
                return [
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'product' => $this->when($this->product, function () {
                return [
                    'name' => $this->product->name,
                    'price' => $this->product->price,
                ];
            }),
        ];
    }
}
