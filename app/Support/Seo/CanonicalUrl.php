<?php

namespace App\Support\Seo;

class CanonicalUrl
{
    public function make(?string $path = null): string
    {
        $path ??= request()->getPathInfo();

        $parsedPath = parse_url($path, PHP_URL_PATH);
        $path = is_string($parsedPath) ? $parsedPath : $path;
        $path = preg_replace('#/+#', '/', '/'.trim($path, '/')) ?? '/';
        $segments = array_values(array_filter(explode('/', trim($path, '/')), fn (string $segment): bool => $segment !== ''));
        $segments = array_map(fn (string $segment): string => rawurlencode(rawurldecode($segment)), $segments);
        $path = implode('/', $segments);

        if ($path !== '' && pathinfo($segments[array_key_last($segments)], PATHINFO_EXTENSION) === '') {
            $path .= '/';
        }

        return rtrim((string) config('seo.canonical_origin', 'https://grhs.ae'), '/').'/'.$path;
    }
}
