<?php

namespace App\Rules;

use App\Services\PublicPathService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPublicPath implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! app(PublicPathService::class)->isValid($value)) {
            $fail('The public path must be a valid, non-reserved path beginning with /.');
        }
    }
}
