<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $words = ucwords(fake()->words(5, true)),
            'slug' => Str::slug($words),
            'description' => fake()->sentences(5, true),
            'quantity' => fake()->randomDigit(),
            'rate' => fake()->randomFloat(2, 10, 1000),
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'meta_title' => "{$words} ".config('app.name'),
            'meta_description' => "{$words} ".config('app.name'),
            'meta_keywords' => "{$words} ".config('app.name'),
        ];
    }
}
