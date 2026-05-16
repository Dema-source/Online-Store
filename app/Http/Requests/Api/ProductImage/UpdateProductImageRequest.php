<?php

namespace App\Http\Requests\Api\ProductImage;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        // Only super administrators can update product images
        return $user && $user->hasRole('super_administrator');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['sometimes', 'integer', 'exists:products,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'file' => [
                'sometimes',
                'file',
                'image',
                'mimes:jpg,png',
                'max:2048' // 2MB max
            ],
        ];
    }
}
