<?php

namespace Database\Factories;

use App\Models\Favourite;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Favourite>
 */
class FavouriteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'guest_token' => fake()->optional()->uuid(),
            'product_id' => Product::factory(),
        ];
    }
}
