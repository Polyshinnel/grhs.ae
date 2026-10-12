<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandPageCatalogue extends Model
{
    protected $fillable = [
        'brand_page_id', 'catalogue_file_path', 'button_label', 'sort_order',
    ];

    public function brandPage(): BelongsTo
    {
        return $this->belongsTo(BrandPage::class);
    }
}
