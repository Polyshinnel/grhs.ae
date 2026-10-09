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
        Schema::create('public_paths', function (Blueprint $table) {
            $table->string('public_path', 512)->primary();
            $table->string('page_type');
            $table->unsignedBigInteger('page_id');
            $table->unique(['page_type', 'page_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_paths');
    }
};
