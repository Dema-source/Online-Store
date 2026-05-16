<?php

namespace App\Http\Requests\Api\CartItem;

use Illuminate\Foundation\Http\FormRequest;

class CheckProductsInCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['required', 'integer', 'exists:products,id']
        ];
        // Only require cart_id for admin users
        $user = $this->user();
        if ($user && $user->hasRole('super_administrator')) {
            $rules['cart_id'] = ['required', 'integer', 'exists:carts,id'];
        }

        return $rules;
    }
}
