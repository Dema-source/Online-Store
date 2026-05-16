<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled']),
            'total_price' => fake()->randomFloat(2, 10, 1000),
            'shipping_address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'order_date' => fake()->date(),
        ];
    }
}
