<?php

use App\Models\Brand;
use App\Models\BrandPage;
use App\Models\Category;
use App\Models\CategoryPage;
use App\Support\Seo\CanonicalUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one matching production canonical and Open Graph URL for the home page on a local host', function () {
    config(['app.url' => 'http://localhost:8000']);

    $response = $this->getWithExactUri('/?utm_source=newsletter&campaign=spring#top')->assertOk();

    assertCanonicalMarkup($response->getContent(), 'https://grhs.ae/');
});

it('renders production canonical URLs for contacts and catalogues without query parameters', function (string $path, string $expectedCanonical) {
    config(['app.url' => 'http://localhost:8000']);

    $response = $this->getWithExactUri($path.'?utm_source=local-test')->assertOk();

    assertCanonicalMarkup($response->getContent(), $expectedCanonical);
})->with([
    'contacts' => ['/contacts/', 'https://grhs.ae/contacts/'],
    'catalogues' => ['/catalogues/', 'https://grhs.ae/catalogues/'],
]);

it('renders the stored category path with a trailing slash as canonical', function () {
    CategoryPage::factory()->create([
        'public_path' => '/tableware',
        'is_published' => true,
    ]);

    $response = $this->getWithExactUri('/tableware/')->assertOk();

    assertCanonicalMarkup($response->getContent(), 'https://grhs.ae/tableware/');
});

it('renders a dynamic brand path with a trailing slash as canonical', function () {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create(['name' => 'Le Coq']);
    BrandPage::factory()->for($brand)->for($category)->create([
        'public_path' => '/tableware/le-coq',
        'is_published' => true,
    ]);

    config(['app.url' => 'http://localhost:8000']);

    $response = $this->getWithExactUri('/tableware/le-coq/?utm_medium=local-test')->assertOk();

    assertCanonicalMarkup($response->getContent(), 'https://grhs.ae/tableware/le-coq/');
});

it('does not render canonical metadata for a missing public page', function () {
    $response = $this->get('/missing-public-page/')->assertNotFound();

    expect($response->getContent())
        ->not->toContain('rel="canonical"')
        ->not->toContain('property="og:url"');
});

it('normalizes explicit canonical paths and leaves file names without a trailing slash', function () {
    $canonicalUrl = app(CanonicalUrl::class);

    expect($canonicalUrl->make('https://other.example//tableware///le-coq?utm_source=test#details'))
        ->toBe('https://grhs.ae/tableware/le-coq/')
        ->and($canonicalUrl->make('/catalogues/catalogue.pdf?download=1'))
        ->toBe('https://grhs.ae/catalogues/catalogue.pdf');
});

function assertCanonicalMarkup(string $html, string $expectedCanonical): void
{
    expect(substr_count($html, 'rel="canonical"'))->toBe(1)
        ->and(preg_match('/<link rel="canonical" href="([^"]+)"/', $html, $canonicalMatch))->toBe(1)
        ->and(preg_match('/<meta property="og:url" content="([^"]+)"/', $html, $openGraphMatch))->toBe(1)
        ->and($canonicalMatch[1])->toBe($expectedCanonical)
        ->and($openGraphMatch[1])->toBe($expectedCanonical);
}
