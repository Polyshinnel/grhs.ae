<?php

use App\Filament\Resources\BrandPages\BrandPageResource;
use App\Filament\Resources\Brands\BrandResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\CategoryPages\CategoryPageResource;
use App\Models\Brand;
use App\Models\BrandPage;
use App\Models\Category;
use App\Models\CategoryPage;
use App\Models\User;
use App\Services\PublicPathService;
use Filament\Schemas\Schema;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('allows a category without a public page', function () {
    $category = Category::factory()->create();

    expect($category->categoryPage)->toBeNull();
});

it('allows a brand without a public page', function () {
    $brand = Brand::factory()->create();

    expect($brand->brandPages)->toBeEmpty();
});

it('allows an authenticated admin user to open each public page resource', function (string $path) {
    $user = User::factory()->create();

    $this->actingAs($user)->get($path)->assertOk();
})->with([
    'categories' => '/admin/categories',
    'brands' => '/admin/brands',
    'category pages' => '/admin/category-pages',
    'brand pages' => '/admin/brand-pages',
]);

it('builds the form schemas for each Filament resource', function () {
    $schemas = [
        CategoryResource::form(Schema::make()),
        BrandResource::form(Schema::make()),
        CategoryPageResource::form(Schema::make()),
        BrandPageResource::form(Schema::make()),
    ];

    expect($schemas)->toHaveCount(4);
});

it('allows only one category page per category', function () {
    $category = Category::factory()->create();
    CategoryPage::factory()->for($category)->create(['public_path' => '/tableware']);

    expect(fn () => CategoryPage::factory()->for($category)->create(['public_path' => '/dinnerware']))
        ->toThrow(QueryException::class);
});

it('allows one brand to have pages in different categories', function () {
    $brand = Brand::factory()->create();

    $firstPage = BrandPage::factory()->for($brand)->for(Category::factory())->create(['public_path' => '/barware/aoyama']);
    $secondPage = BrandPage::factory()->for($brand)->for(Category::factory())->create(['public_path' => '/glassware/aoyama']);

    expect($brand->brandPages)->toHaveCount(2)
        ->and($firstPage->category_id)->not->toBe($secondPage->category_id);
});

it('prevents two brand pages for the same brand and category', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();

    BrandPage::factory()->for($brand)->for($category)->create(['public_path' => '/tableware/aoyama']);

    expect(fn () => BrandPage::factory()->for($brand)->for($category)->create(['public_path' => '/tableware/aoyama-2']))
        ->toThrow(QueryException::class);
});

it('allows multiple standalone brand pages when their paths differ', function () {
    $brand = Brand::factory()->create();

    BrandPage::factory()->for($brand)->create(['category_id' => null, 'public_path' => '/ucello']);
    BrandPage::factory()->for($brand)->create(['category_id' => null, 'public_path' => '/ucello-archive']);

    expect($brand->brandPages)->toHaveCount(2);
});

it('uses the brand name and logo as page fallbacks', function () {
    $brand = Brand::factory()->create([
        'name' => 'Aoyama',
        'logo_path' => 'brands/aoyama.webp',
    ]);
    $page = BrandPage::factory()->for($brand)->create([
        'brand_name' => null,
        'logo_path' => null,
    ]);

    expect($page->display_name)->toBe('Aoyama')
        ->and($page->display_logo_path)->toBe('brands/aoyama.webp');
});

it('keeps public paths unique across page types', function () {
    CategoryPage::factory()->create(['public_path' => '/tableware']);
    $brand = Brand::factory()->create();

    expect(fn () => BrandPage::factory()->for($brand)->create(['category_id' => null, 'public_path' => '/tableware']))
        ->toThrow(ValidationException::class);
});

it('moves a page path atomically and releases its previous path', function () {
    $categoryPage = CategoryPage::factory()->create(['public_path' => '/tableware']);
    $categoryPage->update(['public_path' => '/dinnerware']);

    $brand = Brand::factory()->create();
    $brandPage = BrandPage::factory()->for($brand)->create([
        'category_id' => null,
        'public_path' => '/tableware',
    ]);

    expect($categoryPage->fresh()->public_path)->toBe('/dinnerware')
        ->and($brandPage->public_path)->toBe('/tableware');
});

it('rejects reserved public paths', function (string $path) {
    expect(app(PublicPathService::class)->isValid($path))->toBeFalse();
})->with([
    'root' => '/',
    'catalogues' => '/catalogues',
    'contacts' => '/contacts',
    'admin' => '/admin',
    'admin child' => '/admin/login',
    'storage child' => '/storage/images',
    'build child' => '/build/assets',
    'themes child' => '/themes/goldenratio',
]);

it('rejects malformed public paths and permits historical path spellings', function () {
    $service = app(PublicPathService::class);

    expect($service->isValid('/glassware/yoshinyma'))->toBeTrue()
        ->and($service->isValid('https://grhs.ae/tableware'))->toBeFalse()
        ->and($service->isValid('/tableware?preview=1'))->toBeFalse()
        ->and($service->isValid('/tableware#top'))->toBeFalse()
        ->and($service->isValid('/tableware/'))->toBeFalse()
        ->and($service->isValid('/tableware//kenai'))->toBeFalse();
});

it('round trips ordered brand page content blocks', function () {
    $blocks = [
        ['heading' => 'About the brand', 'text' => 'First section', 'image_path' => 'brand-pages/aoyama/about.webp', 'direction' => 'normal'],
        ['heading' => null, 'text' => 'More information', 'image_path' => 'brand-pages/aoyama/collection.webp', 'direction' => 'reverse'],
    ];

    $page = BrandPage::factory()->create(['content_blocks' => $blocks]);
    $reloadedPage = BrandPage::query()->findOrFail($page->id);

    expect($reloadedPage->content_blocks)->toEqual($blocks);
});

it('restricts deleting categories and brands used by public pages', function () {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();
    $categoryPage = CategoryPage::factory()->for($category)->create(['public_path' => '/tableware']);
    $brandPage = BrandPage::factory()->for($brand)->for($category)->create(['public_path' => '/tableware/kenai']);

    expect(fn () => $category->delete())->toThrow(QueryException::class)
        ->and(fn () => $brand->delete())->toThrow(QueryException::class);

    $this->assertModelExists($categoryPage);
    $this->assertModelExists($brandPage);
});

it('defaults pages to unpublished and stores nullable SEO and image fields', function () {
    $categoryPage = CategoryPage::factory()->create([
        'public_path' => '/tableware',
        'seo_title' => null,
        'seo_description' => null,
        'og_image_path' => null,
        'hero_image_path' => null,
    ]);
    $brandPage = BrandPage::factory()->create([
        'public_path' => '/tableware/kenai',
        'seo_title' => null,
        'seo_description' => null,
        'og_image_path' => null,
        'logo_path' => null,
        'hero_image_path' => null,
        'category_image_path' => null,
        'catalogue_file_path' => null,
    ]);

    expect($categoryPage->fresh()->is_published)->toBeFalse()
        ->and($categoryPage->fresh()->seo_title)->toBeNull()
        ->and($categoryPage->fresh()->hero_image_path)->toBeNull()
        ->and($brandPage->fresh()->is_published)->toBeFalse()
        ->and($brandPage->fresh()->seo_description)->toBeNull()
        ->and($brandPage->fresh()->catalogue_file_path)->toBeNull();
});
