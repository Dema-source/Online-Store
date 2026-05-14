<?php

namespace App\Http\Requests\Api\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20'],
            'payment_method' => ['sometimes', 'string', 'in:cash_on_delivery'],
        ];
    }
}
