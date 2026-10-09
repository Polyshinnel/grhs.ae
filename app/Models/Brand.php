<?php

namespace App\Models;

use Database\Factories\BrandFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    /** @use HasFactory<BrandFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'logo_path', 'sort_order'];

    public function brandPages(): HasMany
    {
        return $this->hasMany(BrandPage::class);
    }

    public function catalogues(): HasMany
    {
        return $this->hasMany(Catalogue::class);
    }
}
