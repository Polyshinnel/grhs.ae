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
        Schema::create('category_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->unique()->constrained()->restrictOnDelete();
            $table->string('public_path', 512)->unique();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('og_image_path')->nullable();
            $table->string('hero_heading');
            $table->text('hero_text')->nullable();
            $table->string('hero_image_path')->nullable();
            $table->boolean('header_black')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_pages');
    }
};
