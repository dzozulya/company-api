<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_versions', function (Blueprint $table): void {
            $table->id();

            $table->morphs('versionable');
            $table->unsignedInteger('version');
            $table->string('event', 20);

            $table->json('snapshot');
            $table->json('diff')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->text('comment')->nullable();

            $table->timestamps();

            $table->unique(
                ['versionable_type', 'versionable_id', 'version'],
                'entity_versions_unique_version'
            );


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_versions');
    }
};
