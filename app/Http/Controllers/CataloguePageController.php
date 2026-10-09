<?php

namespace App\Http\Controllers;

use App\Models\Catalogue;
use App\Models\CataloguePageSettings;
use App\Support\Schema\BreadcrumbSchema;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CataloguePageController extends Controller
{
    public function index(BreadcrumbSchema $breadcrumbSchema): View
    {
        $catalogues = Catalogue::query()
            ->with(['brand:id,name', 'category:id,name,sort_order', 'concepts:id,name'])
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $brands = $catalogues->pluck('brand')->filter()->unique('id')->sortBy(fn ($brand) => Str::lower($brand->name))->values();
        $categories = $catalogues->pluck('category')->filter()->unique('id')->sortBy(fn ($category) => [$category->sort_order, $category->id])->values();
        $concepts = $catalogues->flatMap->concepts->unique('id')->sortBy(fn ($concept) => Str::lower($concept->name))->values();
        $cataloguesByCategory = $catalogues->groupBy('category_id');
        $disk = Storage::disk('public');

        $catalogues->each(function (Catalogue $catalogue) use ($disk): void {
            $catalogue->setAttribute('image_url', $catalogue->image_path ? $disk->url($catalogue->image_path) : null);
            $viewPath = $catalogue->compressed_pdf_path ?: $catalogue->original_pdf_path;
            $catalogue->setAttribute('view_url', $viewPath ? $disk->url($viewPath) : null);
            $catalogue->setAttribute('download_url', $catalogue->original_pdf_path || $catalogue->compressed_pdf_path
                ? route('catalogues.download', $catalogue)
                : null);
        });

        return view('pages.catalogues', [
            'catalogues' => $catalogues,
            'cataloguesByCategory' => $cataloguesByCategory,
            'brands' => $brands,
            'categories' => $categories,
            'concepts' => $concepts,
            'settings' => CataloguePageSettings::query()->find(1),
            'breadcrumbSchema' => $breadcrumbSchema->make([
                ['name' => 'Home', 'url' => 'https://grhs.ae/'],
                ['name' => 'Catalogues', 'url' => 'https://grhs.ae/catalogues'],
            ], 'https://grhs.ae/catalogues'),
        ]);
    }

    public function download(int $catalogue): BinaryFileResponse|StreamedResponse
    {
        $record = Catalogue::query()
            ->where('is_published', true)
            ->findOrFail($catalogue);
        $path = $record->original_pdf_path ?: $record->compressed_pdf_path;

        abort_unless($this->isSafeStoragePath($path), 404);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);

        return $disk->download($path, 'catalogue-'.$record->getKey().'.pdf', [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function isSafeStoragePath(?string $path): bool
    {
        return filled($path)
            && ! str_starts_with($path, '/')
            && ! str_contains($path, '\\')
            && collect(explode('/', $path))->every(fn (string $segment) => $segment !== '' && $segment !== '.' && $segment !== '..');
    }
}
