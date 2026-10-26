<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE products ADD CONSTRAINT chk_products_price_non_negative CHECK (rental_price >= 0);');
            DB::statement('ALTER TABLE products ADD CONSTRAINT chk_products_stock_non_negative CHECK (stock >= 0);');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE products DROP CHECK chk_products_price_non_negative;');
            DB::statement('ALTER TABLE products DROP CHECK chk_products_stock_non_negative;');
        }
    }
};
