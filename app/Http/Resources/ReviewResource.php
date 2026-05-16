<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'profile_id' => $this->profile_id,
            'product_id' => $this->product_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            
            // Relations
            'profile' => $this->when($this->profile, function () {
                return [
                    'phone' => $this->profile->phone,
                    'address' => $this->profile->address,
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
