<?php

namespace App\Services;

use Illuminate\Support\Str;

class PublicPathService
{
    /**
     * Trim accidental whitespace while preserving the path exactly otherwise.
     */
    public function normalize(string $path): string
    {
        return trim($path);
    }

    public function isValid(string $path): bool
    {
        if ($path !== $this->normalize($path) || ! str_starts_with($path, '/') || str_contains($path, '//')) {
            return false;
        }

        if (str_contains($path, '?') || str_contains($path, '#') || str_contains($path, '\\')) {
            return false;
        }

        if ($path !== '/' && str_ends_with($path, '/')) {
            return false;
        }

        $lowerPath = Str::lower($path);

        foreach (['/', '/catalogues', '/contacts'] as $reservedPath) {
            if ($lowerPath === $reservedPath) {
                return false;
            }
        }

        foreach (['/admin', '/storage', '/build', '/themes'] as $reservedPrefix) {
            if ($lowerPath === $reservedPrefix || str_starts_with($lowerPath, $reservedPrefix.'/')) {
                return false;
            }
        }

        return true;
    }
}
