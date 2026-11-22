<?php

declare(strict_types=1);

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
        Schema::create('gallery_images', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 150);
            $table->string('category', 80)->default('Sin clasificar')->index();
            $table->string('image_path');
            $table->string('original_name')->nullable();
            $table->string('file_hash', 64)->nullable()->index();
            $table->foreignId('montaje_id')->nullable()->constrained('montajes')->nullOnDelete();
            $table->string('notes', 500)->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
