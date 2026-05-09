<?php

namespace App\Http\Requests\Api\Cart;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        
        // Only super administrators can update carts
        return $user && $user->hasRole('super_administrator');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $cartId = $this->route('id');
        
        return [
            'profile_id' => ['sometimes', 'integer', 'exists:profiles,id', 'unique:carts,profile_id,'.$cartId],
        ];
    }
}
