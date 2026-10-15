<?php

use App\Http\Controllers\CataloguePageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactInquiryController;
use App\Http\Controllers\PublicPageController;
use App\Http\Middleware\NormalizePublicPageTrailingSlash;
use App\Models\ContactSettings;
use App\Support\Schema\OrganizationSchema;
use Illuminate\Support\Facades\Route;

Route::get('/', function (OrganizationSchema $organizationSchema) {
    return view('pages.home', [
        'organizationSchema' => $organizationSchema->make(ContactSettings::query()->find(1)),
        'websiteSchema' => [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Golden Ratio Hospitality Supplies',
            'alternateName' => 'GRHS',
            'url' => 'https://grhs.ae/',
        ],
    ]);
})->name('home');

Route::get('/catalogues', [CataloguePageController::class, 'index'])
    ->middleware(NormalizePublicPageTrailingSlash::class)
    ->name('catalogues.index');
Route::view('/legal-notice', 'pages.legal', [
    'title' => 'Legal notice',
    'canonical' => '/legal-notice',
])->name('legal-notice');
Route::view('/terms-and-conditions', 'pages.legal', [
    'title' => 'Terms & conditions',
    'canonical' => '/terms-and-conditions',
])->name('terms-and-conditions');
Route::view('/privacy-policy', 'pages.legal', [
    'title' => 'Privacy policy',
    'canonical' => '/privacy-policy',
])->name('privacy-policy');
Route::view('/cookies-policy', 'pages.legal', [
    'title' => 'Cookies policy',
    'canonical' => '/cookies-policy',
])->name('cookies-policy');
Route::get('/contacts', [ContactController::class, 'index'])
    ->middleware(NormalizePublicPageTrailingSlash::class)
    ->name('contacts.index');
Route::post('/contacts/enquiry', [ContactInquiryController::class, 'contact'])
    ->middleware('throttle:contact-inquiries')
    ->name('contacts.enquiry');
Route::post('/quick-contact', [ContactInquiryController::class, 'quick'])
    ->middleware('throttle:contact-inquiries')
    ->name('contacts.quick');
Route::get('/catalogues/{catalogue}/download', [CataloguePageController::class, 'download'])
    ->whereNumber('catalogue')
    ->name('catalogues.download');
Route::get('/brand-catalogues/{brandPage}/download', [PublicPageController::class, 'downloadBrandCatalogue'])
    ->whereNumber('brandPage')
    ->name('brand-catalogues.legacy-download');
Route::get('/brand-catalogues/{publicPath}/{catalogue}/download', [PublicPageController::class, 'downloadBrandCatalogueByPath'])
    ->where(['publicPath' => '.+', 'catalogue' => '[0-9]+'])
    ->name('brand-catalogues.catalogue-download');
Route::get('/brand-catalogues/{publicPath}/download', [PublicPageController::class, 'downloadBrandCatalogueByPath'])
    ->where('publicPath', '.+')
    ->name('brand-catalogues.download');

Route::get('/{publicPath}', [PublicPageController::class, 'show'])
    ->middleware(NormalizePublicPageTrailingSlash::class)
    ->where('publicPath', '(?!(?:admin|catalogues|contacts|storage|build|themes)(?:/|$)|.*\.[^/]+$).+')
    ->name('public-pages.show');
