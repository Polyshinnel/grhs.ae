<?php

use App\Http\Controllers\CataloguePageController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/catalogues', [CataloguePageController::class, 'index'])->name('catalogues.index');
Route::get('/catalogues/{catalogue}/download', [CataloguePageController::class, 'download'])
    ->whereNumber('catalogue')
    ->name('catalogues.download');

Route::get('/{publicPath}', [PublicPageController::class, 'show'])
    ->where('publicPath', '(?!(?:admin|catalogues|contacts|storage|build|themes)(?:/|$)|.*\.[^/]+$).+')
    ->name('public-pages.show');
