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
        Schema::table('quote_logs', function (Blueprint $table): void {
            if (! Schema::hasColumn('quote_logs', 'customer_id')) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('customers')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('quote_logs', 'customer_phone')) {
                $table->string('customer_phone', 30)
                    ->nullable()
                    ->after('customer_name');
            }

            if (! Schema::hasColumn('quote_logs', 'internal_notes')) {
                $table->text('internal_notes')
                    ->nullable()
                    ->after('status');
            }
        });

        Schema::table('contact_messages', function (Blueprint $table): void {
            if (! Schema::hasColumn('contact_messages', 'customer_id')) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('customers')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            if (Schema::hasColumn('contact_messages', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }
        });

        Schema::table('quote_logs', function (Blueprint $table): void {
            if (Schema::hasColumn('quote_logs', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }

            $columns = array_values(array_filter(
                ['customer_phone', 'internal_notes'],
                static fn (string $column): bool => Schema::hasColumn('quote_logs', $column),
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
