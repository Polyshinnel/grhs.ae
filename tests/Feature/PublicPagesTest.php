<?php

use App\Models\Brand;
use App\Models\BrandPage;
use App\Models\Category;
use App\Models\CategoryPage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders a published category page at its stored public path', function () {
    $page = CategoryPage::factory()->create([
        'public_path' => '/tableware',
        'hero_heading' => 'TABLEWARE',
        'is_published' => true,
    ]);

    $this->get('/tableware')
        ->assertOk()
        ->assertSee('TABLEWARE')
        ->assertSee('rel="canonical" href="'.url('/tableware').'"', false);
});

it('returns 404 for an unpublished category page', function () {
    CategoryPage::factory()->create([
        'public_path' => '/tableware',
        'is_published' => false,
    ]);

    $this->get('/tableware')->assertNotFound();
});

it('renders a published brand page at its stored public path', function () {
    $brand = Brand::factory()->create(['name' => 'Kenai']);
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/tableware/kenai',
        'brand_name' => 'Kenai Ceramics',
        'is_published' => true,
    ]);

    $this->get('/tableware/kenai')
        ->assertOk()
        ->assertSee('Kenai Ceramics')
        ->assertSee('rel="canonical" href="'.url('/tableware/kenai').'"', false);
});

it('returns 404 for an unpublished brand page', function () {
    $brand = Brand::factory()->create();
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/tableware/kenai',
        'is_published' => false,
    ]);

    $this->get('/tableware/kenai')->assertNotFound();
});

it('renders a published standalone brand page at its stored root path', function () {
    $brand = Brand::factory()->create(['name' => 'Uccello']);
    BrandPage::factory()->for($brand)->create([
        'category_id' => null,
        'public_path' => '/ucello',
        'is_published' => true,
    ]);

    $this->get('/ucello')
        ->assertOk()
        ->assertSee('Uccello');
});

it('chooses category grid layout from the published brand count', function (int $brandCount, string $gridVariant) {
    $category = Category::factory()->create();
    CategoryPage::factory()->for($category)->create([
        'public_path' => '/tableware',
        'is_published' => true,
    ]);

    foreach (range(1, $brandCount) as $index) {
        $brand = Brand::factory()->create(['name' => "Brand {$index}"]);
        BrandPage::factory()->for($brand)->for($category)->create([
            'public_path' => "/tableware/brand-{$index}",
            'is_published' => true,
            'sort_order' => $index,
        ]);
    }

    $this->get('/tableware')
        ->assertOk()
        ->assertSee('data-brand-grid="'.$gridVariant.'"', false);
})->with([
    'one brand' => [1, 'single'],
    'two brands' => [2, 'double'],
    'three brands' => [3, 'grid'],
    'more than three brands' => [5, 'grid'],
]);

it('shows only published brand pages in sort order and links to their stored paths', function () {
    $category = Category::factory()->create();
    CategoryPage::factory()->for($category)->create([
        'public_path' => '/wood',
        'is_published' => true,
    ]);
    $laterBrand = Brand::factory()->create(['name' => 'Later Brand']);
    $earlierBrand = Brand::factory()->create(['name' => 'Earlier Brand']);
    $draftBrand = Brand::factory()->create(['name' => 'Draft Brand']);
    BrandPage::factory()->for($laterBrand)->for($category)->create([
        'public_path' => '/wood/later-brand',
        'is_published' => true,
        'sort_order' => 20,
    ]);
    BrandPage::factory()->for($earlierBrand)->for($category)->create([
        'public_path' => '/wood/historical-brand-url',
        'is_published' => true,
        'sort_order' => 10,
    ]);
    BrandPage::factory()->for($draftBrand)->for($category)->create([
        'public_path' => '/wood/draft-brand',
        'is_published' => false,
        'sort_order' => 1,
    ]);

    $this->get('/wood')
        ->assertOk()
        ->assertSee('href="/wood/historical-brand-url"', false)
        ->assertSee('href="/wood/later-brand"', false)
        ->assertDontSee('href="/wood/draft-brand"', false)
        ->assertDontSee('Draft Brand');
});

it('renders an empty category brand grid without placeholder cards', function () {
    CategoryPage::factory()->create([
        'public_path' => '/empty-category',
        'is_published' => true,
    ]);

    $this->get('/empty-category')
        ->assertOk()
        ->assertSee('data-brand-grid="grid"', false)
        ->assertSee('data-brand-count="0"', false);
});

it('accepts a historical trailing slash without redirecting', function () {
    $brand = Brand::factory()->create(['name' => 'Ishizuka']);
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/glassware/ishizuka',
        'is_published' => true,
    ]);

    $this->get('/glassware/ishizuka/')
        ->assertOk()
        ->assertHeaderMissing('Location');
});

it('uses brand logo and name fallbacks and a default open graph image', function () {
    $brand = Brand::factory()->create([
        'name' => 'Aoyama Glass',
        'logo_path' => 'brands/aoyama-logo.webp',
    ]);
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/glassware/aoyama',
        'brand_name' => null,
        'logo_path' => null,
        'og_image_path' => null,
        'is_published' => true,
    ]);

    $this->get('/glassware/aoyama')
        ->assertOk()
        ->assertSee('Aoyama Glass')
        ->assertSee('src="/storage/brands/aoyama-logo.webp"', false)
        ->assertSee('property="og:image" content="'.asset('images/home/main-poster.jpg').'"', false);
});

it('uses the uploaded open graph image as an absolute URL', function () {
    $brand = Brand::factory()->create();
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/glassware/og-image',
        'og_image_path' => 'brand-pages/og.webp',
        'is_published' => true,
    ]);

    $this->get('/glassware/og-image')
        ->assertOk()
        ->assertSee('property="og:image" content="'.url('/storage/brand-pages/og.webp').'"', false);
});

it('renders normal and reverse blocks safely and omits empty headings', function () {
    $brand = Brand::factory()->create();
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/glassware/blocks',
        'is_published' => true,
        'content_blocks' => [
            ['heading' => 'About the brand', 'text' => '<script>alert(1)</script>', 'image_path' => 'brand-pages/normal.webp', 'direction' => 'normal'],
            ['heading' => null, 'text' => 'Second block', 'image_path' => 'brand-pages/reverse.webp', 'direction' => 'reverse'],
        ],
    ]);

    $this->get('/glassware/blocks')
        ->assertOk()
        ->assertSee('data-content-direction="normal"', false)
        ->assertSee('data-content-direction="reverse"', false)
        ->assertSee('lg:order-2', false)
        ->assertSee('lg:order-1', false)
        ->assertSee('About the brand')
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('Second block');
});

it('renders a block without a heading or image as a single text column', function () {
    $brand = Brand::factory()->create();
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/glassware/text-only',
        'is_published' => true,
        'content_blocks' => [
            ['heading' => null, 'text' => 'Text without media', 'image_path' => null, 'direction' => 'normal'],
        ],
    ]);

    $this->get('/glassware/text-only')
        ->assertOk()
        ->assertSee('Text without media')
        ->assertDontSee('mb-5 text-xl font-semibold tracking-wide', false)
        ->assertDontSee('aspect-[4/3]', false);
});

it('omits the catalogue section when no PDF is set', function () {
    $brand = Brand::factory()->create(['name' => 'Birdy']);
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/barware/birdy',
        'is_published' => true,
        'catalogue_file_path' => null,
    ]);

    $this->get('/barware/birdy')
        ->assertOk()
        ->assertDontSee('Download the catalog');
});

it('links to the uploaded PDF through the public filesystem disk', function () {
    $brand = Brand::factory()->create(['name' => 'Birdy']);
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/barware/birdy',
        'is_published' => true,
        'catalogue_file_path' => 'brand-pages/catalogues/birdy.pdf',
    ]);

    $this->get('/barware/birdy')
        ->assertOk()
        ->assertSee('href="/storage/brand-pages/catalogues/birdy.pdf"', false)
        ->assertSee('Download the catalog');
});

it('uses the configured header theme on public pages', function (bool $headerBlack, string $theme) {
    CategoryPage::factory()->create([
        'public_path' => '/header-category',
        'is_published' => true,
        'header_black' => $headerBlack,
    ]);

    $this->get('/header-category')
        ->assertOk()
        ->assertSee('data-header-theme="'.$theme.'"', false);
})->with([
    'standard header' => [false, 'white'],
    'black header' => [true, 'black'],
]);

it('keeps the home, admin, and reserved system paths outside public page resolution', function () {
    $this->get('/')->assertOk()->assertSee('Quality hospitality');
    $this->get('/admin')->assertRedirect('/admin/login');
    $this->get('/catalogues')->assertOk();
    $this->get('/contacts')->assertOk()->assertSee('Contact GRHS');
    $this->get('/storage/missing-file.webp')->assertForbidden();
    $this->get('/build/missing.js')->assertNotFound();
    $this->get('/images/site/favicon.svg')->assertNotFound();
});
