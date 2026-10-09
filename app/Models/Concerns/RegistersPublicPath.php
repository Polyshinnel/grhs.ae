<?php

namespace App\Models\Concerns;

use App\Services\PublicPathService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait RegistersPublicPath
{
    public function save(array $options = []): bool
    {
        return DB::transaction(function () use ($options): bool {
            $path = app(PublicPathService::class)->normalize((string) $this->public_path);

            if (! app(PublicPathService::class)->isValid($path)) {
                throw ValidationException::withMessages([
                    'public_path' => 'The public path must be valid and must not be reserved.',
                ]);
            }

            $this->public_path = $path;
            $oldPath = $this->exists ? $this->getOriginal('public_path') : null;

            if (! parent::save($options)) {
                return false;
            }

            try {
                $registeredPath = DB::table('public_paths')->where('public_path', $path)->first();

                if ($registeredPath && ($registeredPath->page_type !== static::class || (int) $registeredPath->page_id !== (int) $this->getKey())) {
                    throw ValidationException::withMessages([
                        'public_path' => 'This public path is already assigned to another page.',
                    ]);
                }

                if ($oldPath && $oldPath !== $path) {
                    $updated = DB::table('public_paths')
                        ->where('public_path', $oldPath)
                        ->where('page_type', static::class)
                        ->where('page_id', $this->getKey())
                        ->update(['public_path' => $path]);

                    if ($updated === 0) {
                        DB::table('public_paths')->insert([
                            'public_path' => $path,
                            'page_type' => static::class,
                            'page_id' => $this->getKey(),
                        ]);
                    }
                } elseif (! $registeredPath) {
                    DB::table('public_paths')->insert([
                        'public_path' => $path,
                        'page_type' => static::class,
                        'page_id' => $this->getKey(),
                    ]);
                }
            } catch (QueryException $exception) {
                throw ValidationException::withMessages([
                    'public_path' => 'This public path is already assigned to another page.',
                ]);
            }

            return true;
        });
    }

    public function delete(): ?bool
    {
        return DB::transaction(function (): ?bool {
            $path = $this->getOriginal('public_path');
            $deleted = parent::delete();

            if ($deleted && $path) {
                DB::table('public_paths')
                    ->where('public_path', $path)
                    ->where('page_type', static::class)
                    ->where('page_id', $this->getKey())
                    ->delete();
            }

            return $deleted;
        });
    }
}
