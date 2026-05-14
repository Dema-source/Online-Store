<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled']),
            'transaction_id' => fake()->uuid(),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'paid_at' => fake()->dateTime(),
        ];
    }
}
