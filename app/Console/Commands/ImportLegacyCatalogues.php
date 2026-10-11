<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Catalogue;
use App\Models\Category;
use App\Models\Concept;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ImportLegacyCatalogues extends Command
{
    protected $signature = 'catalogues:import-legacy
                            {--source= : Directory containing the legacy catalogue files}
                            {--dry-run : Validate and report without copying files or writing records}';

    protected $description = 'Import legacy catalogues from the inventory into Catalogue Library';

    /** @var array<string, Category> */
    private array $categories = [];

    /** @var array<string, Concept> */
    private array $concepts = [];

    /** @var array<string, Brand> */
    private array $brandsByName = [];

    /** @var array<string, Brand> */
    private array $brandsBySlug = [];

    /** @var list<string> */
    private array $skipped = [];

    /** @var list<string> */
    private array $warnings = [];

    public function handle(): int
    {
        $sourceRoot = $this->option('source');

        if (! is_string($sourceRoot) || trim($sourceRoot) === '' || ! is_dir($sourceRoot)) {
            $this->components->error('Provide an existing source directory with --source=...');

            return self::FAILURE;
        }

        $inventoryPath = base_path('docs/legacy-catalogues-inventory.md');

        if (! is_file($inventoryPath)) {
            $this->components->error("Catalogue inventory was not found: {$inventoryPath}");

            return self::FAILURE;
        }

        try {
            $catalogues = $this->readInventory($inventoryPath);
        } catch (Throwable $exception) {
            $this->components->error("Could not read catalogue inventory: {$exception->getMessage()}");

            return self::FAILURE;
        }

        try {
            $this->loadDirectories();
        } catch (Throwable $exception) {
            $this->components->error("Could not load Catalogue Library directories: {$exception->getMessage()}");

            return self::FAILURE;
        }

        $disk = Storage::disk('public');
        $createdCount = 0;

        foreach ($catalogues as $catalogueData) {
            $catalogue = $this->prepareCatalogue($catalogueData, $sourceRoot);

            if ($catalogue === null) {
                continue;
            }

            if ($this->option('dry-run')) {
                $createdCount++;

                continue;
            }

            $copiedPaths = [];

            try {
                foreach ($catalogue['files'] as $file) {
                    if ($disk->exists($file['destination'])) {
                        if ($this->filesMatch($file['source'], $file['destination'])) {
                            continue;
                        }

                        throw new RuntimeException("Destination already exists with different contents: {$file['destination']}");
                    }

                    $stream = fopen($file['source'], 'rb');

                    if ($stream === false) {
                        throw new RuntimeException("Could not open source file: {$file['source']}");
                    }

                    try {
                        if (! $disk->put($file['destination'], $stream)) {
                            throw new RuntimeException("Could not write file: {$file['destination']}");
                        }
                    } finally {
                        fclose($stream);
                    }

                    $copiedPaths[] = $file['destination'];
                }

                DB::transaction(function () use ($catalogue): void {
                    $record = Catalogue::query()->create([
                        'brand_id' => $catalogue['brand']->getKey(),
                        'category_id' => $catalogue['category']->getKey(),
                        'image_path' => $catalogue['image_path'],
                        'image_alt' => $catalogue['image_alt'],
                        'original_pdf_path' => $catalogue['original_pdf_path'],
                        'compressed_pdf_path' => $catalogue['compressed_pdf_path'],
                        'sort_order' => $catalogue['sort_order'],
                        'is_published' => true,
                    ]);

                    $record->concepts()->sync($catalogue['concept_ids']);
                });

                $createdCount++;
            } catch (Throwable $exception) {
                foreach ($copiedPaths as $copiedPath) {
                    $disk->delete($copiedPath);
                }

                $this->skipped[] = "{$catalogue['label']}: import failed ({$exception->getMessage()})";
            }
        }

        $this->displayReport(count($catalogues), $createdCount);

        return $this->skipped === [] ? self::SUCCESS : self::FAILURE;
    }

    /** @return list<array{number: int, category: string, brand: string, concepts: list<string>, image: string, original: string, compressed: string}> */
    private function readInventory(string $inventoryPath): array
    {
        $contents = file_get_contents($inventoryPath);

        if ($contents === false) {
            throw new RuntimeException('Could not read inventory file.');
        }

        $catalogues = [];

        foreach (preg_split('/\R/u', $contents) ?: [] as $line) {
            if (! str_starts_with($line, '|') || preg_match('/^\|\s*\d+\s*\|/u', $line) !== 1) {
                continue;
            }

            $columns = array_map('trim', explode('|', trim($line, '|')));

            if (count($columns) !== 7) {
                throw new RuntimeException("Malformed inventory row: {$line}");
            }

            $conceptNames = [];
            $conceptText = preg_replace('/,?\s*\d+\s*\[нет подписи[^]]*]/u', '', $columns[3]) ?? $columns[3];

            if ($conceptText !== '—' && trim($conceptText) !== '') {
                $conceptNames = array_values(array_filter(array_map('trim', explode(',', $conceptText))));
            }

            $catalogues[] = [
                'number' => (int) $columns[0],
                'category' => $columns[1],
                'brand' => $columns[2],
                'concepts' => $conceptNames,
                'image' => $this->inventorySourcePath($columns[4]),
                'original' => $this->inventorySourcePath($columns[5]),
                'compressed' => $this->inventorySourcePath($columns[6]),
            ];
        }

        if ($catalogues === []) {
            throw new RuntimeException('No catalogue rows were found in the inventory.');
        }

        return $catalogues;
    }

    private function inventorySourcePath(string $inventoryPath): string
    {
        $prefix = 'assets/images/catalog-block/';

        if (! str_starts_with($inventoryPath, $prefix)) {
            throw new RuntimeException("Unexpected inventory asset path: {$inventoryPath}");
        }

        return substr($inventoryPath, strlen($prefix));
    }

    private function loadDirectories(): void
    {
        foreach (Brand::query()->get() as $brand) {
            $this->brandsByName[Str::lower(trim($brand->name))] = $brand;
            $this->brandsBySlug[$brand->slug] = $brand;
        }

        foreach (Category::query()->get() as $category) {
            $this->categories[$category->slug] = $category;
        }

        foreach (Concept::query()->get() as $concept) {
            $this->concepts[Str::lower(trim($concept->name))] = $concept;
        }
    }

    /** @param array{number: int, category: string, brand: string, concepts: list<string>, image: string, original: string, compressed: string} $catalogueData
     * @return array{label: string, brand: Brand, category: Category, concept_ids: list<int>, files: list<array{source: string, destination: string}>, image_path: string, image_alt: string, original_pdf_path: string, compressed_pdf_path: string, sort_order: int}|null
     */
    private function prepareCatalogue(array $catalogueData, string $sourceRoot): ?array
    {
        $label = "#{$catalogueData['number']} {$catalogueData['brand']} ({$catalogueData['category']})";
        $category = $this->categories[$catalogueData['category']] ?? null;
        $brand = $this->findBrand($catalogueData['brand']);

        if (! $brand || ! $category) {
            $missingDirectory = implode(', ', array_filter([
                $brand ? null : "brand '{$catalogueData['brand']}'",
                $category ? null : "category '{$catalogueData['category']}'",
            ]));
            $this->skipped[] = "{$label}: missing {$missingDirectory}";

            return null;
        }

        $sourceImage = $this->imageSourcePath($catalogueData['image']);
        $imagePath = 'catalogues/images/'.$catalogueData['category'].'/'.pathinfo($sourceImage, PATHINFO_FILENAME).'.webp';
        $originalPath = 'catalogues/original/'.$catalogueData['original'];
        $compressedPath = 'catalogues/compressed/'.$catalogueData['compressed'];
        $files = [
            ['source' => $sourceRoot.'/'.$sourceImage, 'destination' => $imagePath],
            ['source' => $sourceRoot.'/'.$catalogueData['original'], 'destination' => $originalPath],
            ['source' => $sourceRoot.'/'.$catalogueData['compressed'], 'destination' => $compressedPath],
        ];

        $existingCatalogue = Catalogue::query()
            ->where(function ($query) use ($imagePath, $originalPath, $compressedPath): void {
                $query->where('image_path', $imagePath)
                    ->orWhere('original_pdf_path', $originalPath)
                    ->orWhere('compressed_pdf_path', $compressedPath);
            })
            ->first();

        if ($existingCatalogue) {
            $this->skipped[] = "{$label}: already exists as catalogue #{$existingCatalogue->getKey()}";

            return null;
        }

        foreach ($files as $file) {
            if (! is_file($file['source']) || ! is_readable($file['source'])) {
                $this->skipped[] = "{$label}: missing or unreadable file {$file['source']}";

                return null;
            }
        }

        $disk = Storage::disk('public');

        foreach ($files as $file) {
            if ($disk->exists($file['destination']) && ! $this->filesMatch($file['source'], $file['destination'])) {
                $this->skipped[] = "{$label}: destination already exists ({$file['destination']})";

                return null;
            }
        }

        $conceptIds = [];

        if ($catalogueData['concepts'] === []) {
            $conceptIds = Concept::query()->pluck('id')->all();
        } else {
            foreach ($catalogueData['concepts'] as $conceptName) {
                $concept = $this->concepts[Str::lower(trim($conceptName))] ?? null;

                if ($concept) {
                    $conceptIds[] = (int) $concept->getKey();
                } else {
                    $this->warnings[] = "{$label}: concept '{$conceptName}' was not found and was ignored";
                }
            }
        }

        $normalizedBrandName = preg_replace('/[^a-z0-9]+/u', ' ', Str::lower($brand->name)) ?? $brand->name;
        $imageAlt = trim($normalizedBrandName).' '.str_replace('-', ' ', $category->slug).' catalog img';

        return [
            'label' => $label,
            'brand' => $brand,
            'category' => $category,
            'concept_ids' => array_values(array_unique($conceptIds)),
            'files' => $files,
            'image_path' => $imagePath,
            'image_alt' => Str::limit($imageAlt, 255, ''),
            'original_pdf_path' => $originalPath,
            'compressed_pdf_path' => $compressedPath,
            'sort_order' => $catalogueData['number'],
        ];
    }

    private function findBrand(string $name): ?Brand
    {
        $normalizedName = Str::lower(trim($name));
        $brand = $this->brandsByName[$normalizedName] ?? $this->brandsBySlug[Str::slug($name)] ?? null;

        if ($brand) {
            return $brand;
        }

        return null;
    }

    private function imageSourcePath(string $inventoryPath): string
    {
        $directory = dirname($inventoryPath);
        $filename = pathinfo($inventoryPath, PATHINFO_FILENAME).'.webp';

        return $directory.'/'.$filename;
    }

    private function filesMatch(string $sourcePath, string $destinationPath): bool
    {
        $sourceHash = hash_file('sha256', $sourcePath);
        $destinationStream = Storage::disk('public')->readStream($destinationPath);

        if ($sourceHash === false || ! is_resource($destinationStream)) {
            return false;
        }

        $destinationHash = hash_init('sha256');

        try {
            hash_update_stream($destinationHash, $destinationStream);
        } finally {
            fclose($destinationStream);
        }

        return hash_equals($sourceHash, hash_final($destinationHash));
    }

    private function displayReport(int $totalCount, int $createdCount): void
    {
        $resultLabel = $this->option('dry-run') ? 'eligible' : 'imported';
        $this->components->info("Inventory rows: {$totalCount}; {$resultLabel}: {$createdCount}; skipped: ".count($this->skipped).'; concept warnings: '.count($this->warnings));

        if ($this->skipped !== []) {
            $this->components->warn('Skipped catalogues:');

            foreach ($this->skipped as $reason) {
                $this->line("- {$reason}");
            }
        }

        if ($this->warnings !== []) {
            $this->components->warn('Concepts not found:');

            foreach ($this->warnings as $warning) {
                $this->line("- {$warning}");
            }
        }

        if ($this->option('dry-run')) {
            $this->comment('Dry run only: no files or database records were changed.');
        }
    }
}
