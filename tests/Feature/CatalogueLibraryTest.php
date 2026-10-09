<?php

use App\Filament\Pages\CataloguePageSettings as CataloguePageSettingsPage;
use App\Filament\Resources\Catalogues\CatalogueResource;
use App\Filament\Resources\Catalogues\Pages\ManageCatalogues;
use App\Filament\Resources\Concepts\ConceptResource;
use App\Models\Brand;
use App\Models\BrandPage;
use App\Models\Catalogue;
use App\Models\CataloguePageSettings;
use App\Models\Category;
use App\Models\Concept;
use App\Models\User;
use Filament\Schemas\Schema;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('creates concepts and catalogues linked to existing directories', function () {
    $concept = Concept::factory()->create(['name' => 'Italian dining']);
    $catalogue = Catalogue::factory()->create();
    $catalogue->concepts()->sync([$concept->id]);

    expect($concept->catalogues)->toHaveCount(1)
        ->and($catalogue->brand)->toBeInstanceOf(Brand::class)
        ->and($catalogue->category)->toBeInstanceOf(Category::class)
        ->and($catalogue->concepts->sole()->is($concept))->toBeTrue();
});

it('allows multiple catalogues with identical directory assignments', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    $concept = Concept::factory()->create();

    $catalogues = Catalogue::factory()->count(2)->for($brand)->for($category)->create();
    $catalogues->each(fn (Catalogue $catalogue) => $catalogue->concepts()->sync([$concept->id]));

    expect($brand->catalogues)->toHaveCount(2);
});

it('allows one catalogue to have multiple concepts', function () {
    $catalogue = Catalogue::factory()->create();
    $concepts = Concept::factory()->count(2)->create();

    $catalogue->concepts()->sync($concepts->modelKeys());

    expect($catalogue->fresh()->concepts)->toHaveCount(2)
        ->and($concepts->first()->catalogues)->toHaveCount(1)
        ->and($concepts->last()->catalogues)->toHaveCount(1);
});

it('creates catalogues with multiple concepts through Filament', function () {
    $this->actingAs(User::factory()->create());
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    $concepts = Concept::factory()->count(2)->create();

    Livewire::test(ManageCatalogues::class)
        ->callAction('create', [
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'concepts' => $concepts->modelKeys(),
            'sort_order' => 0,
            'is_published' => false,
        ])
        ->assertHasNoFormErrors();

    $catalogue = Catalogue::query()->firstOrFail();

    expect($catalogue->concepts)->toHaveCount(2)
        ->and($catalogue->brand->is($brand))->toBeTrue()
        ->and($catalogue->category->is($category))->toBeTrue();
});

it('allows catalogues without matching public pages', function () {
    $catalogue = Catalogue::factory()->create();

    expect($catalogue->brand->brandPages)->toBeEmpty()
        ->and($catalogue->category->categoryPage)->toBeNull();
});

it('defaults catalogues to unpublished and accepts missing media files', function () {
    $catalogue = Catalogue::factory()->create([
        'image_path' => null,
        'original_pdf_path' => null,
        'compressed_pdf_path' => null,
    ]);

    expect($catalogue->is_published)->toBeFalse()
        ->and($catalogue->image_path)->toBeNull()
        ->and($catalogue->original_pdf_path)->toBeNull()
        ->and($catalogue->compressed_pdf_path)->toBeNull();
});

it('reopens saved image and pdf paths in the catalogue edit form', function () {
    Storage::fake('public');
    Storage::disk('public')->put('catalogues/images/card.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/S5sAAAAASUVORK5CYII='));
    Storage::disk('public')->put('catalogues/original/catalogue.pdf', '%PDF-1.4');
    Storage::disk('public')->put('catalogues/compressed/catalogue.pdf', '%PDF-1.4');
    $catalogue = Catalogue::factory()->create([
        'image_path' => 'catalogues/images/card.png',
        'original_pdf_path' => 'catalogues/original/catalogue.pdf',
        'compressed_pdf_path' => 'catalogues/compressed/catalogue.pdf',
    ]);

    Livewire::test(ManageCatalogues::class)
        ->mountTableAction('edit', $catalogue->getKey())
        ->assertTableActionDataSet([
            'image_path' => 'catalogues/images/card.png',
            'original_pdf_path' => 'catalogues/original/catalogue.pdf',
            'compressed_pdf_path' => 'catalogues/compressed/catalogue.pdf',
        ]);
});

it('prevents deleting concepts brands and categories used by catalogues', function () {
    $catalogue = Catalogue::factory()->create();
    $concept = Concept::factory()->create();
    $catalogue->concepts()->sync([$concept->id]);

    expect(fn () => $concept->delete())->toThrow(QueryException::class)
        ->and(fn () => $catalogue->brand->delete())->toThrow(QueryException::class)
        ->and(fn () => $catalogue->category->delete())->toThrow(QueryException::class);
});

it('prevents deleting brands and categories used by public pages', function () {
    $brandPage = BrandPage::factory()->create();
    $category = $brandPage->category;

    expect(fn () => $brandPage->brand->delete())->toThrow(QueryException::class)
        ->and(fn () => $category->delete())->toThrow(QueryException::class);
});

it('persists and reloads the single catalogue page settings record', function () {
    $settings = CataloguePageSettings::singleton();
    $settings->update([
        'heading' => 'Catalogue Library',
        'seo_title' => 'PDF catalogues',
        'seo_description' => 'Browse our catalogues.',
        'intro_text' => 'Choose a brand or concept.',
        'header_black' => true,
    ]);

    $reloadedSettings = CataloguePageSettings::query()->findOrFail(1);

    expect($reloadedSettings->heading)->toBe('Catalogue Library')
        ->and($reloadedSettings->seo_title)->toBe('PDF catalogues')
        ->and($reloadedSettings->header_black)->toBeTrue()
        ->and(CataloguePageSettings::query()->count())->toBe(1);
});

it('saves catalogue page settings through its Filament page', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CataloguePageSettingsPage::class)
        ->fillForm([
            'heading' => 'Catalogue Library',
            'seo_title' => 'PDF catalogues',
            'seo_description' => 'Browse our catalogues.',
            'intro_text' => 'Choose a brand or concept.',
            'header_black' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(CataloguePageSettings::query()->count())->toBe(1)
        ->and(CataloguePageSettings::query()->firstOrFail()->seo_title)->toBe('PDF catalogues');
});

it('exposes one authenticated settings page and builds the catalogue forms', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/catalogue-page-settings')->assertOk();

    expect(CatalogueResource::form(Schema::make()))->toBeInstanceOf(Schema::class)
        ->and(ConceptResource::form(Schema::make()))->toBeInstanceOf(Schema::class)
        ->and(CataloguePageSettingsPage::getNavigationLabel())->toBe('Catalogue Page Settings')
        ->and(CataloguePageSettings::query()->count())->toBeLessThanOrEqual(1);
});

it('keeps catalogue pdf changes independent from brand page pdf files', function () {
    $brandPage = BrandPage::factory()->create(['catalogue_file_path' => 'brand-pages/original.pdf']);
    $catalogue = Catalogue::factory()->create([
        'brand_id' => $brandPage->brand_id,
        'original_pdf_path' => 'catalogues/original/first.pdf',
    ]);

    $catalogue->update(['original_pdf_path' => 'catalogues/original/second.pdf']);

    expect($catalogue->fresh()->original_pdf_path)->toBe('catalogues/original/second.pdf')
        ->and($brandPage->fresh()->catalogue_file_path)->toBe('brand-pages/original.pdf');
});
