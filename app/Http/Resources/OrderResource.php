<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property int $profile_id
 * @property string $status
 * @property float $total_price
 * @property string|null $shipping_address
 * @property string|null $phone
 * @property date|null $order_date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\OrderItem[] $orderItems
 * @property \App\Models\Payment|null $payment
 * @property \App\Models\Profile|null $profile
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'profile_id' => $this->profile_id,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'shipping_address' => $this->shipping_address,
            'phone' => $this->phone,
            'order_date' => $this->order_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'profile' => $this->whenLoaded('profile', new ProfileResource($this->profile)),
        ];
    }
}
