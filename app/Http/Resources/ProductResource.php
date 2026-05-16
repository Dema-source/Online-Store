<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductResource
 *
 * @mixin \App\Models\Product
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()),
            'description' => $this->getTranslation('description', app()->getLocale()),
            'brand' => $this->brand,
            'price' => $this->price,
            'stock' => $this->stock,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relations
            'categories' => $this->when($this->categories, function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->getTranslation('name', app()->getLocale()),
                        'created_at' => $category->created_at,
                        'updated_at' => $category->updated_at,
                    ];
                });
            }),
            'categories_count' => $this->whenCounted('categories'),
            'images' => $this->when($this->images, function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'product_id' => $image->product_id,
                        'image_url' => $image->image_url,
                        'created_at' => $image->created_at,
                        'updated_at' => $image->updated_at,
                    ];
                });
            }),
            'images_count' => $this->whenCounted('images'),
        ];
    }
}
