<?php

use App\Models\Brand;
use App\Models\Catalogue;
use App\Models\CataloguePageSettings;
use App\Models\Category;
use App\Models\Concept;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function createPublishedCatalogue(array $attributes = [], array $concepts = []): Catalogue
{
    $catalogue = Catalogue::factory()->create(array_merge(['is_published' => true], $attributes));

    if ($concepts !== []) {
        $catalogue->concepts()->sync(collect($concepts)->map(fn (Concept $concept) => $concept->getKey())->all());
    }

    return $catalogue;
}

it('renders the public catalogue page before the dynamic public page route', function () {
    $this->get('/catalogues')
        ->assertOk()
        ->assertSee('Catalogue Library')
        ->assertSee(route('catalogues.index'), false);

    expect(route('catalogues.index'))->toEndWith('/catalogues');
});

it('shows only published catalogues and keeps repeated brands as separate cards in sort order', function () {
    $brand = Brand::factory()->create(['name' => 'Aoyama']);
    $firstCategory = Category::factory()->create(['name' => 'Glassware']);
    $secondCategory = Category::factory()->create(['name' => 'Tableware']);
    $later = createPublishedCatalogue(['brand_id' => $brand->id, 'category_id' => $firstCategory->id, 'sort_order' => 20]);
    $earlierSort = createPublishedCatalogue(['brand_id' => $brand->id, 'category_id' => $firstCategory->id, 'sort_order' => 10]);
    $earlierIdTie = createPublishedCatalogue(['brand_id' => $brand->id, 'category_id' => $firstCategory->id, 'sort_order' => 10]);
    createPublishedCatalogue(['brand_id' => $brand->id, 'category_id' => $secondCategory->id, 'sort_order' => 5]);
    $draft = Catalogue::factory()->create(['brand_id' => $brand->id, 'is_published' => false]);

    $response = $this->get('/catalogues')->assertOk()
        ->assertSee('Aoyama')
        ->assertSee('Glassware')
        ->assertSee('Tableware')
        ->assertDontSee('data-catalogue-card data-brand-id="'.$draft->brand_id.'" data-category-id="'.$draft->category_id.'"', false);

    expect(substr_count($response->getContent(), 'data-catalogue-card'))->toBe(4)
        ->and(strpos($response->getContent(), 'data-category-group-id="'.$firstCategory->id.'"'))
        ->toBeLessThan(strpos($response->getContent(), 'data-category-group-id="'.$secondCategory->id.'"'))
        ->and(strpos($response->getContent(), 'data-catalogue-id="'.$earlierSort->id.'"'))
        ->toBeLessThan(strpos($response->getContent(), 'data-catalogue-id="'.$earlierIdTie->id.'"'))
        ->and(strpos($response->getContent(), 'data-catalogue-id="'.$earlierIdTie->id.'"'))
        ->toBeLessThan(strpos($response->getContent(), 'data-catalogue-id="'.$later->id.'"'))
        ->and($earlierSort->sort_order)->toBe($earlierIdTie->sort_order)
        ->and(strpos($response->getContent(), 'data-category-id="'.$firstCategory->id.'" data-concept-ids="'))
        ->toBeLessThan(strrpos($response->getContent(), 'data-category-id="'.$firstCategory->id.'" data-concept-ids="'));
});

it('renders linked concepts, filter IDs from published catalogues, and an image placeholder', function () {
    $brand = Brand::factory()->create(['name' => 'Visible Brand']);
    $category = Category::factory()->create(['name' => 'Visible Category']);
    $concept = Concept::factory()->create(['name' => 'Classic']);
    $unusedBrand = Brand::factory()->create(['name' => 'Draft Brand']);
    $unusedCategory = Category::factory()->create(['name' => 'Draft Category']);
    $unusedConcept = Concept::factory()->create(['name' => 'Unused Concept']);
    createPublishedCatalogue(['brand_id' => $brand->id, 'category_id' => $category->id], [$concept]);
    $draft = Catalogue::factory()->for($unusedBrand)->for($unusedCategory)->create(['is_published' => false]);
    $draft->concepts()->sync([$unusedConcept->id]);

    $this->get('/catalogues')->assertOk()
        ->assertSee('Visible Brand')
        ->assertSee('Visible Category')
        ->assertSee('data-catalogue-category-group', false)
        ->assertSee('Classic')
        ->assertSee('data-brand-id="'.$brand->id.'"', false)
        ->assertSee('data-category-id="'.$category->id.'"', false)
        ->assertSee('data-concept-ids="'.$concept->id.'"', false)
        ->assertSee('value="'.$brand->id.'"', false)
        ->assertSee('value="'.$category->id.'"', false)
        ->assertSee('value="'.$concept->id.'"', false)
        ->assertDontSee('Draft Brand')
        ->assertDontSee('Draft Category')
        ->assertDontSee('Unused Concept')
        ->assertSee('aria-label="No catalogue image"', false);
});

it('prefers the compressed PDF for viewing and the original PDF for downloading', function () {
    Storage::fake('public');
    Storage::disk('public')->put('catalogues/original/catalogue.pdf', '%PDF-original');
    Storage::disk('public')->put('catalogues/compressed/catalogue.pdf', '%PDF-compressed');
    $catalogue = createPublishedCatalogue([
        'original_pdf_path' => 'catalogues/original/catalogue.pdf',
        'compressed_pdf_path' => 'catalogues/compressed/catalogue.pdf',
    ]);

    $this->get('/catalogues')->assertOk()
        ->assertSee('href="/storage/catalogues/compressed/catalogue.pdf"', false)
        ->assertSee('href="'.route('catalogues.download', $catalogue).'"', false);

    $this->get(route('catalogues.download', $catalogue))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertDownload('catalogue-'.$catalogue->id.'.pdf');
});

it('uses the original PDF for viewing and compressed PDF for downloading as fallbacks', function () {
    Storage::fake('public');
    Storage::disk('public')->put('catalogues/original/only.pdf', '%PDF-original');
    Storage::disk('public')->put('catalogues/compressed/only.pdf', '%PDF-compressed');
    $viewOnly = createPublishedCatalogue(['original_pdf_path' => 'catalogues/original/only.pdf']);
    $downloadOnly = createPublishedCatalogue(['compressed_pdf_path' => 'catalogues/compressed/only.pdf']);

    $this->get('/catalogues')->assertOk()
        ->assertSee('href="/storage/catalogues/original/only.pdf"', false)
        ->assertSee('href="/storage/catalogues/compressed/only.pdf"', false);

    $this->get(route('catalogues.download', $downloadOnly))
        ->assertOk()
        ->assertDownload('catalogue-'.$downloadOnly->id.'.pdf');

    expect($viewOnly->original_pdf_path)->toBe('catalogues/original/only.pdf');
});

it('does not offer file actions when no PDF is set and blocks draft or unsafe downloads', function () {
    Storage::fake('public');
    $empty = createPublishedCatalogue();
    $draft = Catalogue::factory()->create(['is_published' => false, 'original_pdf_path' => 'catalogues/draft.pdf']);
    Storage::disk('public')->put('catalogues/draft.pdf', '%PDF-draft');
    $unsafe = createPublishedCatalogue(['original_pdf_path' => '../private/secret.pdf']);

    $this->get('/catalogues')->assertOk()
        ->assertDontSee(route('catalogues.download', $empty), false);
    $this->get(route('catalogues.download', $draft))->assertNotFound();
    $this->get(route('catalogues.download', $unsafe))->assertNotFound();
});

it('uses page settings for SEO and header theme, with safe defaults when settings are absent', function () {
    $this->get('/catalogues')->assertOk()
        ->assertSee('<title>Product catalogues | GRHS</title>', false)
        ->assertSee('property="og:image" content="'.asset('images/home/main-poster.jpg').'"', false)
        ->assertSee('data-header-theme="white"', false);

    expect(CataloguePageSettings::query()->count())->toBe(0);

    CataloguePageSettings::query()->forceCreate([
        'id' => 1,
        'seo_title' => 'Catalogue SEO title',
        'seo_description' => 'Catalogue SEO description',
        'og_image_path' => 'catalogues/page-settings/og.webp',
        'heading' => 'Our catalogues',
        'intro_text' => 'Browse our selected collections.',
        'header_black' => true,
    ]);

    $this->get('/catalogues')->assertOk()
        ->assertSee('<title>Catalogue SEO title</title>', false)
        ->assertSee('<meta name="description" content="Catalogue SEO description">', false)
        ->assertSee('property="og:title" content="Catalogue SEO title"', false)
        ->assertSee('property="og:description" content="Catalogue SEO description"', false)
        ->assertSee('property="og:image" content="'.url('/storage/catalogues/page-settings/og.webp').'"', false)
        ->assertSee('rel="canonical" href="'.url('/catalogues').'"', false)
        ->assertSee('Our catalogues')
        ->assertSee('Browse our selected collections.')
        ->assertSee('data-header-theme="black"', false);

    expect(CataloguePageSettings::query()->count())->toBe(1);
});
