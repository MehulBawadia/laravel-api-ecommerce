<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
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
            'meta_title' => "{$name} ".config('app.name'),
            'meta_description' => "{$name} ".config('app.name'),
            'meta_keywords' => "{$name} ".config('app.name'),
        ];
    }
}
