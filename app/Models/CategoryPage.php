<?php

namespace App\Models;

use App\Models\Concerns\RegistersPublicPath;
use Database\Factories\CategoryPageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryPage extends Model
{
    /** @use HasFactory<CategoryPageFactory> */
    use HasFactory;

    use RegistersPublicPath;

    protected $fillable = [
        'category_id', 'public_path', 'seo_title', 'seo_description', 'og_image_path',
        'hero_heading', 'hero_text', 'hero_image_path', 'hero_image_alt', 'content_blocks', 'header_black', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'content_blocks' => 'array',
            'header_black' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
