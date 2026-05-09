<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            
            // Relations
            'cart' => $this->whenLoaded('cart'),
            'product' => $this->whenLoaded('product'),
            
            // Calculated fields
            'subtotal' => $this->whenLoaded('product', function () {
                return ($this->product->price ?? 0) * $this->quantity;
            }),
            'product_name' => $this->whenLoaded('product', function () {
                return $this->product->name ?? null;
            }),
            'product_price' => $this->whenLoaded('product', function () {
                return $this->product->price ?? 0;
            }),
            'formatted_subtotal' => $this->whenLoaded('product', function () {
                return number_format(($this->product->price ?? 0) * $this->quantity, 2);
            }),
        ];
    }
}
