<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class NormalizePublicPageTrailingSlash
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $path = parse_url($request->getRequestUri(), PHP_URL_PATH) ?: '/';

        if ((! $request->isMethod('GET') && ! $request->isMethod('HEAD')) || $path === '/' || str_ends_with($path, '/') || ! $response->isSuccessful()) {
            return $response;
        }

        $canonicalUrl = $path.'/';
        $queryString = $request->getQueryString();

        if ($queryString !== null) {
            $canonicalUrl .= '?'.$queryString;
        }

        return new RedirectResponse($canonicalUrl, 301);
    }
}
