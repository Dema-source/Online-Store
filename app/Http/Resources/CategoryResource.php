<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CategoryResource
 *
 * @mixin \App\Models\Category
 */
class CategoryResource extends JsonResource
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relations
            'products' => $this->when($this->products, function () {
                return $this->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->getTranslation('name', app()->getLocale()),
                        'description' => $product->getTranslation('description', app()->getLocale()),
                        'brand' => $product->brand,
                        'price' => $product->price,
                        'stock' => $product->stock,
                        'created_at' => $product->created_at,
                        'updated_at' => $product->updated_at,
                        'images' => $this->when($product->images, function () use ($product) {
                            return $product->images->map(function ($image) {
                                return [
                                    'id' => $image->id,
                                    'product_id' => $image->product_id,
                                    'image_url' => $image->image_url,
                                    'created_at' => $image->created_at,
                                    'updated_at' => $image->updated_at,
                                ];
                            });
                        }),
                        'images_count' => $product->images_count ?? $product->images->count(),
                    ];
                });
            }),
            'products_count' => $this->whenCounted('products'),
        ];
    }
}
