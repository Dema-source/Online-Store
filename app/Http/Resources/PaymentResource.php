<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property int $order_id
 * @property float $amount
 * @property string $status
 * @property string|null $transaction_id
 * @property string|null $payment_method
 * @property \Carbon\Carbon|null $paid_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\Order|null $order
 */
class PaymentResource extends JsonResource
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
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'payment_method' => $this->payment_method,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'order' => $this->whenLoaded('order', new OrderResource($this->order)),
        ];
    }
}
