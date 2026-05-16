<?php

namespace App\Http\Requests\Api\CartItem;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StoreCartItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:999'],
        ];

        // Only require cart_id for admin users
        $user = $this->user();
        if ($user && $user->hasRole('super_administrator')) {
            $rules['cart_id'] = ['required', 'integer', 'exists:carts,id'];
        }

        return $rules;
    }

}
