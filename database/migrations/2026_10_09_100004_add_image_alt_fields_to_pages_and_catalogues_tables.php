<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('category_pages', function (Blueprint $table): void {
            $table->string('hero_image_alt')->nullable();
        });
        Schema::table('brand_pages', function (Blueprint $table): void {
            $table->string('hero_image_alt')->nullable();
            $table->string('logo_alt')->nullable();
            $table->string('category_image_alt')->nullable();
        });
        Schema::table('catalogues', function (Blueprint $table): void {
            $table->string('image_alt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogues', function (Blueprint $table): void {
            $table->dropColumn('image_alt');
        });
        Schema::table('brand_pages', function (Blueprint $table): void {
            $table->dropColumn(['hero_image_alt', 'logo_alt', 'category_image_alt']);
        });
        Schema::table('category_pages', function (Blueprint $table): void {
            $table->dropColumn('hero_image_alt');
        });
    }
};
