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
        // Índices de alto rendimiento para el Catálogo de Productos
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'is_featured'], 'idx_products_active_featured');
            $table->index(['is_active', 'category_id'], 'idx_products_active_category');
            $table->index(['is_active', 'name'], 'idx_products_active_name');
            $table->index('rental_price', 'idx_products_rental_price');
        });

        // Índices para Preguntas Frecuentes
        Schema::table('faqs', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'idx_faqs_active_sort');
            $table->index('category', 'idx_faqs_category');
        });

        // Índices para Mensajes de Contacto
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->index(['is_read', 'created_at'], 'idx_contact_messages_read_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_active_featured');
            $table->dropIndex('idx_products_active_category');
            $table->dropIndex('idx_products_active_name');
            $table->dropIndex('idx_products_rental_price');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->dropIndex('idx_faqs_active_sort');
            $table->dropIndex('idx_faqs_category');
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropIndex('idx_contact_messages_read_created');
        });
    }
};
