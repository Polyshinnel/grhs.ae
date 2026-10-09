<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Catalogue extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id', 'category_id', 'image_path', 'image_alt', 'original_pdf_path',
        'compressed_pdf_path', 'sort_order', 'is_published',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function concepts(): BelongsToMany
    {
        return $this->belongsToMany(Concept::class);
    }
}
