<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CataloguePageSettings extends Model
{
    protected $fillable = [
        'seo_title', 'seo_description', 'og_image_path', 'heading', 'intro_text', 'header_black',
    ];

    protected function casts(): array
    {
        return ['header_black' => 'boolean'];
    }

    public static function singleton(): self
    {
        $settings = static::query()->find(1);

        if ($settings !== null) {
            return $settings;
        }

        $settings = new static;
        $settings->id = 1;
        $settings->heading = 'Catalogue Library';
        $settings->save();

        return $settings;
    }
}
