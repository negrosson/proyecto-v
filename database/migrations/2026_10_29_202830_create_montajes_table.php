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
        Schema::create('montajes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('slug', 150)->unique();
            $table->string('client', 150)->nullable();
            $table->string('type', 100)->default('Boda & Banquetería');
            $table->string('year', 20)->default('2025');
            $table->string('location', 150)->nullable();
            $table->string('capacity', 150)->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('items_used')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_featured')->default(true)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('montajes');
    }
};
