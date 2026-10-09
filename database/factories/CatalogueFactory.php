<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Catalogue;
use App\Models\Category;
use App\Models\Concept;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Catalogue>
 */
class CatalogueFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(function (Catalogue $catalogue): void {
            if (! $catalogue->concepts()->exists()) {
                $catalogue->concepts()->attach(Concept::factory()->create());
            }
        });
    }

    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'image_path' => null,
            'original_pdf_path' => null,
            'compressed_pdf_path' => null,
            'sort_order' => 0,
            'is_published' => false,
        ];
    }
}
