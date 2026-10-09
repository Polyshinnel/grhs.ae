<?php

namespace App\Http\Controllers;

use App\Models\BrandPage;
use App\Models\CategoryPage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublicPageController extends Controller
{
    public function show(string $publicPath): View
    {
        $path = '/'.trim($publicPath, '/');
        $registeredPath = DB::table('public_paths')->where('public_path', $path)->first();

        if (! $registeredPath) {
            throw new NotFoundHttpException;
        }

        if ($registeredPath->page_type === CategoryPage::class) {
            $page = CategoryPage::query()
                ->with([
                    'category.brandPages' => fn ($query) => $query
                        ->where('is_published', true)
                        ->with('brand')
                        ->orderBy('sort_order')
                        ->orderBy('id'),
                ])
                ->whereKey($registeredPath->page_id)
                ->where('public_path', $path)
                ->where('is_published', true)
                ->firstOrFail();

            return view('pages.category', ['page' => $page]);
        }

        if ($registeredPath->page_type === BrandPage::class) {
            $page = BrandPage::query()
                ->with('brand')
                ->whereKey($registeredPath->page_id)
                ->where('public_path', $path)
                ->where('is_published', true)
                ->firstOrFail();

            return view('pages.brand', ['page' => $page]);
        }

        throw new NotFoundHttpException;
    }
}
