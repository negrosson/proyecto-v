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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('clicks_count')->default(0)->after('stock')->index();
            $table->unsignedBigInteger('quoted_count')->default(0)->after('clicks_count')->index();
            $table->unsignedBigInteger('units_quoted_count')->default(0)->after('quoted_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['clicks_count']);
            $table->dropIndex(['quoted_count']);
            $table->dropColumn(['clicks_count', 'quoted_count', 'units_quoted_count']);
        });
    }
};
