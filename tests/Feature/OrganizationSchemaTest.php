<?php

use App\Models\Brand;
use App\Models\BrandPage;
use App\Models\Category;
use App\Models\CategoryPage;
use App\Models\ContactSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function organizationSchemaFrom(string $html): array
{
    preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);

    expect($matches[1])->toHaveCount(1);

    return json_decode($matches[1][0], true, flags: JSON_THROW_ON_ERROR);
}

it('renders one valid organization schema on the home page and contacts page', function (string $path) {
    $response = $this->get($path)->assertOk();

    $schema = organizationSchemaFrom($response->getContent());

    expect($schema['@context'])->toBe('https://schema.org')
        ->and($schema['@type'])->toBe('WholesaleStore')
        ->and($schema['@id'])->toBe('https://grhs.ae/#organization')
        ->and($schema['logo'])->toBe('https://grhs.ae/images/site/logo.png')
        ->and($schema['name'])->toBe('GRHS')
        ->and($schema['legalName'])->toBe('GRHS Trading LLC');
})->with([
    'home page' => ['/'],
    'contacts page' => ['/contacts'],
]);

it('uses contact settings for phone email and postal address', function () {
    ContactSettings::query()->forceCreate([
        'id' => 1,
        'phone' => '+971 58 533 8524',
        'email' => 'schema@example.com',
        'address' => 'Office 2203, 22nd floor, Ontario Tower, Business Bay, Dubai, UAE',
        'social_links' => [],
    ]);

    $schema = organizationSchemaFrom($this->get('/')->assertOk()->getContent());

    expect($schema['telephone'])->toBe('+971585338524')
        ->and($schema['email'])->toBe('schema@example.com')
        ->and($schema['address'])->toBe([
            '@type' => 'PostalAddress',
            'streetAddress' => 'Office 2203, 22nd floor, Ontario Tower, Business Bay',
            'addressLocality' => 'Dubai',
            'addressCountry' => 'AE',
        ]);
});

it('includes ordered unique valid social profiles and omits messaging channels', function () {
    ContactSettings::query()->forceCreate([
        'id' => 1,
        'social_links' => [
            ['platform' => 'instagram', 'text' => '@first', 'url' => 'https://www.instagram.com/first/'],
            ['platform' => 'whatsapp', 'text' => 'WhatsApp', 'url' => 'https://wa.me/971585338524'],
            ['platform' => 'telegram', 'text' => 'Telegram', 'url' => 'https://t.me/grhs'],
            ['platform' => 'facebook', 'text' => 'Fake text', 'url' => 'javascript:alert(1)'],
            ['platform' => 'instagram', 'text' => 'Wrong host', 'url' => 'https://example.com/grhs'],
            ['platform' => 'facebook', 'text' => 'Facebook', 'url' => 'https://www.facebook.com/grhs/'],
            ['platform' => 'instagram', 'text' => '@first again', 'url' => 'https://www.instagram.com/first/'],
        ],
    ]);

    $schema = organizationSchemaFrom($this->get('/contacts')->assertOk()->getContent());

    expect($schema['sameAs'])->toBe([
        'https://www.instagram.com/first/',
        'https://www.facebook.com/grhs/',
    ]);
});

it('omits empty contact properties and renders when settings are absent', function () {
    $schema = organizationSchemaFrom($this->get('/contacts')->assertOk()->getContent());

    expect($schema)->not->toHaveKeys(['telephone', 'email', 'address', 'sameAs']);

    ContactSettings::query()->forceCreate([
        'id' => 1,
        'phone' => '',
        'email' => '',
        'address' => '  ',
        'social_links' => [null, ['platform' => 'instagram', 'url' => 'not a url']],
    ]);

    $schema = organizationSchemaFrom($this->get('/')->assertOk()->getContent());

    expect($schema)->not->toHaveKeys(['telephone', 'email', 'address', 'sameAs']);
});

it('escapes script-breaking contact values while preserving valid JSON', function () {
    ContactSettings::query()->forceCreate([
        'id' => 1,
        'address' => '</script><script>alert("xss")</script>',
        'social_links' => [],
    ]);

    $html = $this->get('/')->assertOk()->getContent();
    $schema = organizationSchemaFrom($html);

    expect($html)->not->toContain('</script><script>alert("xss")')
        ->and($schema['address']['streetAddress'])->toBe('</script><script>alert("xss")</script>');
});

it('adds only breadcrumb schema to category and brand pages', function () {
    $category = Category::factory()->create();
    CategoryPage::factory()->for($category)->create([
        'public_path' => '/tableware',
        'is_published' => true,
    ]);
    $brand = Brand::factory()->create();
    BrandPage::factory()->for($brand)->create([
        'category_id' => $category->id,
        'public_path' => '/tableware/brand',
        'is_published' => true,
    ]);

    foreach (['/tableware', '/tableware/brand', '/catalogues'] as $path) {
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $this->get($path)->assertOk()->getContent(), $matches);

        expect($matches[1])->toHaveCount(1)
            ->and(json_decode($matches[1][0], true, flags: JSON_THROW_ON_ERROR)['@type'])->toBe('BreadcrumbList');
    }
});
