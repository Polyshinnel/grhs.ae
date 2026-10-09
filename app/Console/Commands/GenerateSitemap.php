<?php

namespace App\Console\Commands;

use App\Models\BrandPage;
use App\Models\CategoryPage;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use RuntimeException;
use Throwable;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the public XML sitemap';

    private const BASE_URL = 'https://grhs.ae';

    public function handle(): int
    {
        $filePath = public_path('sitemap.xml');

        try {
            $categoryPages = CategoryPage::query()
                ->where('is_published', true)
                ->whereExists(fn ($query) => $query
                    ->selectRaw('1')
                    ->from('public_paths')
                    ->whereColumn('public_paths.public_path', 'category_pages.public_path')
                    ->where('public_paths.page_type', CategoryPage::class)
                    ->whereColumn('public_paths.page_id', 'category_pages.id'))
                ->with(['category.brandPages' => fn ($query) => $query
                    ->where('is_published', true)
                    ->with('brand')
                    ->orderBy('id')])
                ->orderBy('public_path')
                ->get()
                ->filter(fn (CategoryPage $page): bool => $this->matchesPublicPageRoute($page->public_path));

            $brandPages = BrandPage::query()
                ->where('is_published', true)
                ->whereExists(fn ($query) => $query
                    ->selectRaw('1')
                    ->from('public_paths')
                    ->whereColumn('public_paths.public_path', 'brand_pages.public_path')
                    ->where('public_paths.page_type', BrandPage::class)
                    ->whereColumn('public_paths.page_id', 'brand_pages.id'))
                ->with('brand')
                ->orderBy('public_path')
                ->get()
                ->filter(fn (BrandPage $page): bool => $this->matchesPublicPageRoute($page->public_path));

            $entries = collect([
                ['url' => self::BASE_URL.'/', 'lastmod' => null],
                ['url' => self::BASE_URL.'/catalogues', 'lastmod' => null],
                ['url' => self::BASE_URL.'/contacts', 'lastmod' => null],
            ]);

            foreach ($categoryPages as $page) {
                $relatedDates = $page->category?->brandPages
                    ->flatMap(fn (BrandPage $brandPage): array => [$brandPage->updated_at, $brandPage->brand?->updated_at])
                    ->all() ?? [];
                $dates = array_merge([$page->updated_at, $page->category?->updated_at], $relatedDates);
                $entries->push($this->entry($page->public_path, $this->latestDate(...$dates)));
            }

            foreach ($brandPages as $page) {
                $entries->push($this->entry(
                    $page->public_path,
                    $this->latestDate($page->updated_at, $page->brand?->updated_at),
                ));
            }

            $entries = $entries->keyBy('url')->sortKeys()->values();
            $xml = $this->renderXml($entries);
            $fileAlreadyExisted = is_file($filePath);

            if ($fileAlreadyExisted && file_get_contents($filePath) === $xml) {
                $this->displayResult($entries->count(), $categoryPages->count(), $brandPages->count(), $filePath, 'unchanged');

                return self::SUCCESS;
            }

            $this->writeAtomically($filePath, $xml);
            $result = $fileAlreadyExisted ? 'updated' : 'created';
            $this->displayResult($entries->count(), $categoryPages->count(), $brandPages->count(), $filePath, $result);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->components->error("Could not generate sitemap at {$filePath}: {$exception->getMessage()}");

            return self::FAILURE;
        }
    }

    /** @return array{url: string, lastmod: ?CarbonInterface} */
    private function entry(string $publicPath, ?CarbonInterface $lastmod): array
    {
        return [
            'url' => self::BASE_URL.'/'.ltrim($publicPath, '/'),
            'lastmod' => $lastmod,
        ];
    }

    private function latestDate(?CarbonInterface ...$dates): ?CarbonInterface
    {
        return collect($dates)
            ->filter()
            ->sortByDesc(fn (CarbonInterface $date): int => $date->getTimestamp())
            ->first();
    }

    private function matchesPublicPageRoute(string $publicPath): bool
    {
        return preg_match('/\.[^\/]+$/', ltrim($publicPath, '/')) !== 1;
    }

    /** @param Collection<int, array{url: string, lastmod: ?CarbonInterface}> $entries */
    private function renderXml(Collection $entries): string
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        foreach ($entries as $entry) {
            $xml .= "    <url>\n";
            $xml .= '        <loc>'.htmlspecialchars($entry['url'], ENT_XML1 | ENT_QUOTES, 'UTF-8')."</loc>\n";

            if ($entry['lastmod']) {
                $xml .= '        <lastmod>'.$entry['lastmod']->utc()->format('Y-m-d\TH:i:sP')."</lastmod>\n";
            }

            $xml .= "    </url>\n";
        }

        return $xml."</urlset>\n";
    }

    private function writeAtomically(string $filePath, string $contents): void
    {
        $temporaryPath = tempnam(dirname($filePath), 'sitemap-');

        if ($temporaryPath === false) {
            throw new RuntimeException('Could not create a temporary file.');
        }

        try {
            if (! chmod($temporaryPath, 0644)) {
                throw new RuntimeException('Could not set the sitemap file permissions.');
            }

            if (file_put_contents($temporaryPath, $contents, LOCK_EX) !== strlen($contents)) {
                throw new RuntimeException('Could not write the complete sitemap.');
            }

            if (! rename($temporaryPath, $filePath)) {
                throw new RuntimeException('Could not replace the sitemap file.');
            }
        } finally {
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }
        }
    }

    private function displayResult(int $urlCount, int $categoryPageCount, int $brandPageCount, string $filePath, string $result): void
    {
        $this->components->info("URLs: {$urlCount}; Category Pages: {$categoryPageCount}; Brand Pages: {$brandPageCount}");
        $this->line("Path: {$filePath}");
        $this->line("Result: {$result}");
    }
}
