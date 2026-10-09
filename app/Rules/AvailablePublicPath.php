<?php

namespace App\Rules;

use App\Models\BrandPage;
use App\Models\CategoryPage;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AvailablePublicPath implements ValidationRule
{
    public function __construct(private readonly ?Model $record = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $query = DB::table('public_paths')->where('public_path', $value);

        if ($this->record instanceof CategoryPage || $this->record instanceof BrandPage) {
            $query->where(function ($query): void {
                $query->where('page_type', '!=', $this->record::class)
                    ->orWhere('page_id', '!=', $this->record->getKey());
            });
        }

        if ($query->exists()) {
            $fail('This public path is already assigned to another page.');
        }
    }
}
