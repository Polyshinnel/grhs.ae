<?php

use App\Models\Brand;
use App\Models\BrandPage;
use App\Models\Category;
use App\Models\CategoryPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->sitemapPublicPath = sys_get_temp_dir().'/grhs-sitemap-'.uniqid();
    mkdir($this->sitemapPublicPath, 0777, true);
    app()->usePublicPath($this->sitemapPublicPath);
});

afterEach(function () {
    foreach (glob($this->sitemapPublicPath.'/*') ?: [] as $path) {
        is_dir($path) ? rmdir($path) : unlink($path);
    }

    rmdir($this->sitemapPublicPath);
    app()->usePublicPath(base_path('public'));
});

it('generates valid XML with static pages and published public page paths', function () {
    $category = Category::factory()->create();
    CategoryPage::factory()->for($category)->create([
        'public_path' => '/餐具&tableware',
        'is_published' => true,
        'updated_at' => '2026-10-08 10:20:30',
    ]);
    DB::table('categories')->where('id', $category->id)->update(['updated_at' => '2026-10-08 10:00:00']);
    CategoryPage::factory()->create(['public_path' => '/draft-category', 'is_published' => false]);
    CategoryPage::factory()->create(['public_path' => '/missing.pdf', 'is_published' => true]);

    $brand = Brand::factory()->create(['updated_at' => '2026-10-08 10:00:00']);
    DB::table('brands')->where('id', $brand->id)->update(['updated_at' => '2026-10-08 10:00:00']);
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/餐具&tableware/brand-one',
        'is_published' => true,
        'updated_at' => '2026-10-09 12:00:00',
    ]);
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/brand-independent-page',
        'is_published' => true,
    ]);
    BrandPage::factory()->for($brand)->create(['public_path' => '/draft-brand', 'is_published' => false]);

    $this->artisan('sitemap:generate')
        ->expectsOutputToContain('URLs: 6; Category Pages: 1; Brand Pages: 2')
        ->expectsOutputToContain('Result: created')
        ->assertSuccessful();

    $xml = file_get_contents($this->sitemapPublicPath.'/sitemap.xml');
    expect($xml)
        ->toContain('https://grhs.ae/</loc>')
        ->toContain('https://grhs.ae/catalogues</loc>')
        ->toContain('https://grhs.ae/contacts</loc>')
        ->toContain('https://grhs.ae/餐具&amp;tableware</loc>')
        ->toContain('https://grhs.ae/餐具&amp;tableware/brand-one</loc>')
        ->toContain('https://grhs.ae/brand-independent-page</loc>')
        ->not->toContain('draft-category')
        ->not->toContain('draft-brand')
        ->not->toContain('missing.pdf')
        ->not->toContain('/admin')
        ->not->toContain('?');

    expect(simplexml_load_string($xml))->not->toBeFalse();
    expect(substr_count($xml, '<loc>'))->toBe(6);
    expect($xml)->toContain('<lastmod>2026-10-09T12:00:00+00:00</lastmod>');
});

it('keeps deterministic output and does not rewrite an unchanged sitemap', function () {
    $this->artisan('sitemap:generate')->assertSuccessful();
    $filePath = $this->sitemapPublicPath.'/sitemap.xml';
    touch($filePath, 1_700_000_000);
    clearstatcache(true, $filePath);
    $modifiedAt = filemtime($filePath);

    $this->artisan('sitemap:generate')
        ->expectsOutputToContain('Result: unchanged')
        ->assertSuccessful();

    clearstatcache(true, $filePath);
    expect(filemtime($filePath))->toBe($modifiedAt);

    CategoryPage::factory()->create(['public_path' => '/new-published-page', 'is_published' => true]);
    $this->artisan('sitemap:generate')
        ->expectsOutputToContain('Result: updated')
        ->assertSuccessful();
    expect(file_get_contents($filePath))->toContain('https://grhs.ae/new-published-page</loc>');
});

it('returns a failure exit code when the sitemap cannot be replaced', function () {
    mkdir($this->sitemapPublicPath.'/sitemap.xml');

    $this->artisan('sitemap:generate')
        ->expectsOutputToContain('Could not generate sitemap')
        ->assertFailed();
});
