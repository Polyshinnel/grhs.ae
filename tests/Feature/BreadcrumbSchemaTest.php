<?php

use App\Models\Brand;
use App\Models\BrandPage;
use App\Models\Category;
use App\Models\CategoryPage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function breadcrumbSchemaFrom(string $html): array
{
    preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);

    $schemas = array_map(fn (string $json): array => json_decode($json, true, flags: JSON_THROW_ON_ERROR), $matches[1]);
    $breadcrumbs = array_values(array_filter($schemas, fn (array $schema): bool => ($schema['@type'] ?? null) === 'BreadcrumbList'));

    expect($breadcrumbs)->toHaveCount(1);

    return $breadcrumbs[0];
}

it('renders one valid catalogue breadcrumb list with production URLs', function () {
    $html = $this->get('/catalogues')->assertOk()->getContent();
    $schema = breadcrumbSchemaFrom($html);
    $items = $schema['itemListElement'];

    expect($schema['@context'])->toBe('https://schema.org')
        ->and($schema['@id'])->toBe('https://grhs.ae/catalogues#breadcrumb')
        ->and($items)->toBe([
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://grhs.ae/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Catalogues', 'item' => 'https://grhs.ae/catalogues'],
        ]);
});

it('uses the category display name and public path in its breadcrumb list', function () {
    $category = Category::factory()->create(['name' => 'Tableware & Dining']);
    CategoryPage::factory()->for($category)->create([
        'public_path' => '/tableware',
        'hero_heading' => 'Explore our complete tabletop solutions | GRHS',
        'is_published' => true,
    ]);

    $schema = breadcrumbSchemaFrom($this->get('/tableware')->assertOk()->getContent());

    expect($schema['itemListElement'])->toBe([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://grhs.ae/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Tableware & Dining', 'item' => 'https://grhs.ae/tableware'],
    ]);
});

it('uses the concrete brand page and published category page for each brand breadcrumb', function () {
    $category = Category::factory()->create(['name' => 'Tableware']);
    CategoryPage::factory()->for($category)->create(['public_path' => '/tableware', 'is_published' => true]);
    $poolware = Category::factory()->create(['name' => 'Poolware']);
    CategoryPage::factory()->for($poolware)->create(['public_path' => '/poolware', 'is_published' => true]);
    $brand = Brand::factory()->create(['name' => 'Original Kenai']);
    BrandPage::factory()->for($brand)->for($category)->create([
        'public_path' => '/tableware/kenai', 'brand_name' => 'Kenai Ceramics', 'is_published' => true,
    ]);
    BrandPage::factory()->for($brand)->for($poolware)->create([
        'public_path' => '/poolware/kenai', 'brand_name' => 'Kenai Pool', 'is_published' => true,
    ]);

    $tableware = breadcrumbSchemaFrom($this->get('/tableware/kenai')->assertOk()->getContent());
    $poolwareSchema = breadcrumbSchemaFrom($this->get('/poolware/kenai')->assertOk()->getContent());

    expect($tableware['itemListElement'])->toBe([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://grhs.ae/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Tableware', 'item' => 'https://grhs.ae/tableware'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Kenai Ceramics', 'item' => 'https://grhs.ae/tableware/kenai'],
    ])->and($poolwareSchema['itemListElement'])->toBe([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://grhs.ae/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Poolware', 'item' => 'https://grhs.ae/poolware'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Kenai Pool', 'item' => 'https://grhs.ae/poolware/kenai'],
    ]);
});

it('omits an unpublished category page from a brand breadcrumb', function () {
    $category = Category::factory()->create(['name' => 'Unpublished Category']);
    CategoryPage::factory()->for($category)->create(['public_path' => '/draft-category', 'is_published' => false]);
    $brand = Brand::factory()->create(['name' => 'Standalone']);
    BrandPage::factory()->for($brand)->for($category)->create([
        'public_path' => '/somewhere/standalone',
        'is_published' => true,
    ]);

    $items = breadcrumbSchemaFrom($this->get('/somewhere/standalone')->assertOk()->getContent())['itemListElement'];

    expect($items)->toBe([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://grhs.ae/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Standalone', 'item' => 'https://grhs.ae/somewhere/standalone'],
    ]);
});

it('omits the category from a brand breadcrumb when no category page exists', function () {
    $category = Category::factory()->create(['name' => 'Category Without Page']);
    $brand = Brand::factory()->create(['name' => 'Independent Brand']);
    BrandPage::factory()->for($brand)->for($category)->create([
        'public_path' => '/category-without-page/independent-brand',
        'is_published' => true,
    ]);

    $items = breadcrumbSchemaFrom($this->get('/category-without-page/independent-brand')->assertOk()->getContent())['itemListElement'];

    expect($items)->toBe([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://grhs.ae/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Independent Brand', 'item' => 'https://grhs.ae/category-without-page/independent-brand'],
    ]);
});

it('escapes script breaking names and encodes special characters in public paths', function () {
    $category = Category::factory()->create(['name' => 'Dining </script><script>alert("x")</script>']);
    CategoryPage::factory()->for($category)->create([
        'public_path' => '/dining & tableware',
        'is_published' => true,
    ]);

    $html = $this->get('/dining%20%26%20tableware')->assertOk()->getContent();
    $schema = breadcrumbSchemaFrom($html);

    expect($html)->not->toContain('</script><script>alert("x")')
        ->and($schema['itemListElement'][1]['name'])->toBe('Dining </script><script>alert("x")</script>')
        ->and($schema['itemListElement'][1]['item'])->toBe('https://grhs.ae/dining%20%26%20tableware');
});

it('does not add breadcrumbs to home and keeps one breadcrumb schema on target pages', function () {
    $category = Category::factory()->create();
    CategoryPage::factory()->for($category)->create(['public_path' => '/category', 'is_published' => true]);
    $brand = Brand::factory()->create();
    BrandPage::factory()->for($brand)->create(['public_path' => '/category/brand', 'is_published' => true]);

    preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $this->get('/')->assertOk()->getContent(), $homeMatches);

    expect(array_filter(array_map(fn (string $json): array => json_decode($json, true, flags: JSON_THROW_ON_ERROR), $homeMatches[1]), fn (array $schema): bool => ($schema['@type'] ?? null) === 'BreadcrumbList'))
        ->toBe([]);

    foreach (['/catalogues', '/category', '/category/brand'] as $path) {
        $schema = breadcrumbSchemaFrom($this->get($path)->assertOk()->getContent());
        $items = $schema['itemListElement'];

        expect(array_column($items, 'position'))->toBe(range(1, count($items)))
            ->and(array_column($items, 'item'))->toHaveCount(count(array_unique(array_column($items, 'item'))));

        foreach (array_column($items, 'item') as $url) {
            expect($url)->toStartWith('https://grhs.ae/')->toBeUrl();
        }

        expect($items[array_key_last($items)]['item'])->toBe('https://grhs.ae'.($path === '/catalogues' ? '/catalogues' : $path));
    }
});
