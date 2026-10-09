<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogue_concept', function (Blueprint $table) {
            $table->foreignId('catalogue_id')->constrained()->restrictOnDelete();
            $table->foreignId('concept_id')->constrained()->restrictOnDelete();
            $table->primary(['catalogue_id', 'concept_id']);
        });

        DB::table('catalogues')
            ->whereNotNull('concept_id')
            ->orderBy('id')
            ->each(function (object $catalogue): void {
                DB::table('catalogue_concept')->insert([
                    'catalogue_id' => $catalogue->id,
                    'concept_id' => $catalogue->concept_id,
                ]);
            });

        Schema::table('catalogues', function (Blueprint $table) {
            $table->dropForeign(['concept_id']);
            $table->dropColumn('concept_id');
        });
    }

    public function down(): void
    {
        $cataloguesWithInvalidConceptCount = DB::table('catalogues')
            ->leftJoin('catalogue_concept', 'catalogues.id', '=', 'catalogue_concept.catalogue_id')
            ->select('catalogues.id')
            ->groupBy('catalogues.id')
            ->havingRaw('COUNT(catalogue_concept.concept_id) != 1')
            ->count();

        if ($cataloguesWithInvalidConceptCount > 0) {
            throw new RuntimeException('Cannot roll back catalogue concepts while any catalogue has zero or multiple concepts.');
        }

        Schema::table('catalogues', function (Blueprint $table) {
            $table->unsignedBigInteger('concept_id')->nullable();
        });

        DB::table('catalogues')
            ->join('catalogue_concept', 'catalogues.id', '=', 'catalogue_concept.catalogue_id')
            ->update(['catalogues.concept_id' => DB::raw('catalogue_concept.concept_id')]);

        Schema::table('catalogues', function (Blueprint $table) {
            $table->unsignedBigInteger('concept_id')->nullable(false)->change();
            $table->foreign('concept_id')->references('id')->on('concepts')->restrictOnDelete();
        });

        Schema::dropIfExists('catalogue_concept');
    }
};
