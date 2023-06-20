<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $user = User::factory()->create(),

            'billing_first_name' => $user->first_name,
            'billing_last_name' => $user->last_name,
            'billing_email' => $user->email,
            'billing_contact' => fake()->phoneNumber(),
            'billing_address_line_1' => fake()->buildingNumber(),
            'billing_address_line_2' => fake()->streetName(),
            'billing_area' => fake()->streetAddress(),
            'billing_landmark' => fake()->streetSuffix(),
            'billing_city' => fake()->city(),
            'billing_postal_code' => fake()->postcode(),
            'billing_state_province' => fake()->citySuffix(),
            'billing_country' => fake()->country(),

            'shipping_first_name' => $user->first_name,
            'shipping_last_name' => $user->last_name,
            'shipping_email' => $user->email,
            'shipping_contact' => fake()->phoneNumber(),
            'shipping_address_line_1' => fake()->buildingNumber(),
            'shipping_address_line_2' => fake()->streetName(),
            'shipping_area' => fake()->streetAddress(),
            'shipping_landmark' => fake()->streetSuffix(),
            'shipping_city' => fake()->city(),
            'shipping_postal_code' => fake()->postcode(),
            'shipping_state_province' => fake()->citySuffix(),
            'shipping_country' => fake()->country(),
        ];
    }
}
