<?php

namespace App\Http\Controllers;

use App\Models\BrandPage;
use App\Models\CategoryPage;
use App\Support\Schema\BreadcrumbSchema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublicPageController extends Controller
{
    public function show(string $publicPath, BreadcrumbSchema $breadcrumbSchema): View
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

            $pageUrl = $this->schemaUrl($page->public_path);

            return view('pages.category', [
                'page' => $page,
                'breadcrumbSchema' => $breadcrumbSchema->make([
                    ['name' => 'Home', 'url' => 'https://grhs.ae/'],
                    ['name' => $page->category->name, 'url' => $pageUrl],
                ], $pageUrl),
            ]);
        }

        if ($registeredPath->page_type === BrandPage::class) {
            $page = BrandPage::query()
                ->with(['brand', 'category.categoryPage' => fn ($query) => $query->where('is_published', true)])
                ->whereKey($registeredPath->page_id)
                ->where('public_path', $path)
                ->where('is_published', true)
                ->firstOrFail();

            $pageUrl = $this->schemaUrl($page->public_path);
            $breadcrumbItems = [['name' => 'Home', 'url' => 'https://grhs.ae/']];

            if ($page->category?->categoryPage) {
                $breadcrumbItems[] = [
                    'name' => $page->category->name,
                    'url' => $this->schemaUrl($page->category->categoryPage->public_path),
                ];
            }

            $breadcrumbItems[] = ['name' => $page->display_name, 'url' => $pageUrl];

            return view('pages.brand', [
                'page' => $page,
                'breadcrumbSchema' => $breadcrumbSchema->make($breadcrumbItems, $pageUrl),
            ]);
        }

        throw new NotFoundHttpException;
    }

    private function schemaUrl(string $publicPath): string
    {
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', trim($publicPath, '/'))));

        return 'https://grhs.ae/'.($encodedPath === '' ? '' : $encodedPath);
    }
}
