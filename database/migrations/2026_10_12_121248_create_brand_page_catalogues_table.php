<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brand_page_catalogues', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_page_id')->constrained()->cascadeOnDelete();
            $table->string('catalogue_file_path');
            $table->string('button_label')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('brand_pages')
            ->whereNotNull('catalogue_file_path')
            ->orderBy('id')
            ->chunk(500, function ($brandPages): void {
                DB::table('brand_page_catalogues')->insert($brandPages->map(fn (object $brandPage): array => [
                    'brand_page_id' => $brandPage->id,
                    'catalogue_file_path' => $brandPage->catalogue_file_path,
                    'button_label' => null,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all());
            });

        Schema::table('brand_pages', function (Blueprint $table): void {
            $table->dropColumn('catalogue_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_pages', function (Blueprint $table): void {
            $table->string('catalogue_file_path')->nullable();
        });

        DB::table('brand_page_catalogues')
            ->select(['brand_page_id', 'catalogue_file_path'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->chunk(500, function ($catalogues): void {
                foreach ($catalogues as $catalogue) {
                    DB::table('brand_pages')
                        ->where('id', $catalogue->brand_page_id)
                        ->whereNull('catalogue_file_path')
                        ->update(['catalogue_file_path' => $catalogue->catalogue_file_path]);
                }
            });

        Schema::dropIfExists('brand_page_catalogues');
    }
};
