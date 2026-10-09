<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\BrandPage;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BrandPage>
 */
class BrandPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'public_path' => '/'.fake()->unique()->slug(),
            'brand_name' => null,
            'seo_title' => null,
            'seo_description' => null,
            'og_image_path' => null,
            'logo_path' => null,
            'hero_image_path' => null,
            'category_image_path' => null,
            'catalogue_file_path' => null,
            'content_blocks' => null,
            'sort_order' => 0,
        ];
    }
}
