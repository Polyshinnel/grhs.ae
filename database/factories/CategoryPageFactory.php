<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoryPage>
 */
class CategoryPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'public_path' => '/'.fake()->unique()->slug(),
            'seo_title' => null,
            'seo_description' => null,
            'og_image_path' => null,
            'hero_heading' => fake()->sentence(3),
            'hero_text' => null,
            'hero_image_path' => null,
        ];
    }
}
