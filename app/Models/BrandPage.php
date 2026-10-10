<?php

namespace App\Models;

use App\Models\Concerns\RegistersPublicPath;
use Database\Factories\BrandPageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandPage extends Model
{
    /** @use HasFactory<BrandPageFactory> */
    use HasFactory;

    use RegistersPublicPath;

    protected $fillable = [
        'brand_id', 'category_id', 'public_path', 'brand_name', 'h1_title', 'seo_title', 'seo_description',
        'og_image_path', 'logo_path', 'logo_alt', 'hero_image_path', 'hero_image_alt', 'category_image_path', 'category_image_alt', 'catalogue_file_path',
        'content_blocks', 'header_black', 'sort_order', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'content_blocks' => 'array',
            'header_black' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->brand_name ?: $this->brand->name;
    }

    public function getDisplayLogoPathAttribute(): ?string
    {
        return $this->logo_path ?: $this->brand->logo_path;
    }
}
