<?php

use App\Models\Brand;
use App\Models\BrandPage;
use App\Models\Catalogue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('redirects a published dynamic page to its slash URL and serves the canonical URL directly', function () {
    $brand = Brand::factory()->create();
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/tableware/le-coq',
        'is_published' => true,
    ]);

    $this->get('/tableware/le-coq')->assertStatus(301)
        ->assertRedirect('/tableware/le-coq/');

    $this->getWithExactUri('/tableware/le-coq/')->assertOk()
        ->assertHeaderMissing('Location');
});

it('preserves the query string when redirecting a dynamic page', function () {
    $brand = Brand::factory()->create();
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/tableware/le-coq',
        'is_published' => true,
    ]);

    $this->get('/tableware/le-coq?utm_source=google')->assertStatus(301)
        ->assertRedirect('/tableware/le-coq/?utm_source=google');
});

it('redirects HEAD requests for published dynamic pages and preserves their query strings', function () {
    $brand = Brand::factory()->create();
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/tableware/le-coq',
        'is_published' => true,
    ]);

    $this->head('/tableware/le-coq')->assertStatus(301)
        ->assertRedirect('/tableware/le-coq/');

    $this->head('/tableware/le-coq?utm_source=google')->assertStatus(301)
        ->assertRedirect('/tableware/le-coq/?utm_source=google');

    $this->headWithExactUri('/tableware/le-coq/')->assertOk()
        ->assertHeaderMissing('Location');
});

it('redirects contact and catalogue pages and serves each canonical URL directly', function () {
    $this->get('/contacts')->assertStatus(301)->assertRedirect('/contacts/');
    $this->getWithExactUri('/contacts/')->assertOk()->assertHeaderMissing('Location');

    $this->get('/catalogues')->assertStatus(301)->assertRedirect('/catalogues/');
    $this->getWithExactUri('/catalogues/')->assertOk()->assertHeaderMissing('Location');
});

it('redirects HEAD requests for contact and catalogue pages and serves canonical URLs directly', function () {
    $this->head('/contacts')->assertStatus(301)->assertRedirect('/contacts/');
    $this->headWithExactUri('/contacts/')->assertOk()->assertHeaderMissing('Location');

    $this->head('/catalogues')->assertStatus(301)->assertRedirect('/catalogues/');
    $this->headWithExactUri('/catalogues/')->assertOk()->assertHeaderMissing('Location');
});

it('leaves the homepage and unrelated routes unchanged', function () {
    $this->get('/')->assertOk()->assertHeaderMissing('Location');
    $this->head('/')->assertOk()->assertHeaderMissing('Location');
    $this->get('/admin')->assertRedirect('/admin/login');

    $this->get('/build/assets/app.js')->assertNotFound()->assertHeaderMissing('Location');
    $this->get('/robots.txt')->assertNotFound()->assertHeaderMissing('Location');
    $this->get('/sitemap.xml')->assertNotFound()->assertHeaderMissing('Location');
    $this->get('/favicon.ico')->assertNotFound()->assertHeaderMissing('Location');
});

it('does not redirect catalogue downloads or contact form submissions', function () {
    Storage::fake('public');
    Storage::disk('public')->put('catalogues/catalogue.pdf', '%PDF-example');
    $catalogue = Catalogue::factory()->create([
        'is_published' => true,
        'original_pdf_path' => 'catalogues/catalogue.pdf',
    ]);

    $this->get(route('catalogues.download', $catalogue))->assertOk()
        ->assertDownload('catalogue-'.$catalogue->id.'.pdf');

    $this->postJson('/contacts/enquiry', [
        'name' => 'Bot',
        'email' => 'bot@example.com',
        'website' => 'spam',
    ])->assertStatus(422);
});

it('keeps unknown and unpublished dynamic pages as 404 responses', function () {
    $this->get('/missing-public-page')->assertNotFound()
        ->assertHeaderMissing('Location');

    $this->head('/missing-public-page')->assertNotFound()
        ->assertHeaderMissing('Location');

    $brand = Brand::factory()->create();
    BrandPage::factory()->for($brand)->create([
        'public_path' => '/tableware/draft-brand',
        'is_published' => false,
    ]);

    $this->get('/tableware/draft-brand')->assertNotFound()
        ->assertHeaderMissing('Location');
});
