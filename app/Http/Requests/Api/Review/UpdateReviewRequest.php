<?php

namespace App\Http\Requests\Api\Review;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
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
        $user = auth()->user();
        $isAdmin = $user && $user->hasRole('super_administrator');
        
        $rules = [
            'product_id' => ['sometimes', 'exists:products,id'],
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'comment' => ['sometimes', 'string', 'max:1000'],
        ];

        // Admins can update profile_id, customers cannot
        if ($isAdmin) {
            $rules['profile_id'] = ['sometimes', 'exists:profiles,id'];
        }

        return $rules;
    }
}
