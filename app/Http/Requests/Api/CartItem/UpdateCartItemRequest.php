<?php

namespace App\Http\Requests\Api\CartItem;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCartItemRequest extends FormRequest
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
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:999'],
            'product_id' => ['sometimes', 'integer', 'exists:products,id'],
        ];
        // Add cart_id validation for admin users
        if (auth()->check() && auth()->user()->hasRole('super_administrator')) {
            $rules['cart_id'] = ['sometimes', 'integer', 'exists:carts,id'];
        }

        return $rules;
    }
}
