<?php

namespace Tests;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    public function getWithExactUri(string $uri): TestResponse
    {
        $request = Request::create(rtrim((string) config('app.url'), '/').$uri, 'GET');
        $kernel = $this->app->make(Kernel::class);
        $response = $kernel->handle($request);

        $kernel->terminate($request, $response);

        return TestResponse::fromBaseResponse($response, $request);
    }

    public function headWithExactUri(string $uri): TestResponse
    {
        $request = Request::create(rtrim((string) config('app.url'), '/').$uri, 'HEAD');
        $kernel = $this->app->make(Kernel::class);
        $response = $kernel->handle($request);

        $kernel->terminate($request, $response);

        return TestResponse::fromBaseResponse($response, $request);
    }
}
