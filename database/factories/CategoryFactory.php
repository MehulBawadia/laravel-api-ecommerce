<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $name = Str::title($this->faker->words(3, true)),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'meta_title' => "{$name} ". config('app.name'),
            'meta_description' => "{$name} ". config('app.name'),
            'meta_keywords' => "{$name} ". config('app.name'),
        ];
    }
}
