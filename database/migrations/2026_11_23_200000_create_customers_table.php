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
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 150)->index();
            $table->string('type', 30)->default('particular')->index();
            $table->string('rut', 25)->nullable();
            $table->string('phone', 30)->nullable()->index();
            $table->string('email', 150)->nullable()->index();
            $table->string('commune', 120)->nullable();
            $table->boolean('is_frequent')->default(false)->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['is_frequent', 'name'], 'idx_customers_frequent_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
