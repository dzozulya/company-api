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
        Schema::create('company_versions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->integer('version');

            $table->string('name',256);
            $table->string('edrpou',10);
            $table->text('address');

            $table->timestamp('created_at');

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_versions');
    }
};
