<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Endurece la integridad referencial y agrega índices compuestos reales
     * para eliminar filesorts y escaneos completos en producción.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->index(['is_active', 'category_id', 'name'], 'idx_products_active_category_name');
            $table->index(['is_active', 'clicks_count', 'quoted_count'], 'idx_products_active_ranking');
        });

        // En MySQL/MariaDB reemplazar ON DELETE CASCADE por ON DELETE RESTRICT para impedir
        // borrado silencioso de todo el inventario al eliminar una categoría desde SQL/phpMyAdmin
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            Schema::table('products', function (Blueprint $table): void {
                $table->dropForeign(['category_id']);
                $table->foreign('category_id')
                    ->references('id')
                    ->on('categories')
                    ->restrictOnDelete();
            });
        }

        Schema::table('montajes', function (Blueprint $table): void {
            $table->index(['is_active', 'sort_order', 'id'], 'idx_montajes_active_sort');
        });

        Schema::table('quote_logs', function (Blueprint $table): void {
            $table->index('created_at', 'idx_quote_logs_created_at');
            $table->index(['status', 'created_at'], 'idx_quote_logs_status_created');
        });

        Schema::table('security_audit_logs', function (Blueprint $table): void {
            $table->index('created_at', 'idx_security_audit_created_at');
            $table->index(['event_type', 'created_at'], 'idx_security_audit_type_created');
        });

        Schema::table('gallery_images', function (Blueprint $table): void {
            $table->index(['category', 'sort_order', 'created_at'], 'idx_gallery_category_sort_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gallery_images', function (Blueprint $table): void {
            $table->dropIndex('idx_gallery_category_sort_created');
        });

        Schema::table('security_audit_logs', function (Blueprint $table): void {
            $table->dropIndex('idx_security_audit_created_at');
            $table->dropIndex('idx_security_audit_type_created');
        });

        Schema::table('quote_logs', function (Blueprint $table): void {
            $table->dropIndex('idx_quote_logs_created_at');
            $table->dropIndex('idx_quote_logs_status_created');
        });

        Schema::table('montajes', function (Blueprint $table): void {
            $table->dropIndex('idx_montajes_active_sort');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            Schema::table('products', function (Blueprint $table): void {
                $table->dropForeign(['category_id']);
                $table->foreign('category_id')
                    ->references('id')
                    ->on('categories')
                    ->cascadeOnDelete();
            });
        }

        Schema::table('products', function (Blueprint $table): void {
            $table->dropIndex('idx_products_active_category_name');
            $table->dropIndex('idx_products_active_ranking');
        });
    }
};
