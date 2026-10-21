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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('code')->unique();       // Cód. del PDF (ej: 10, 220, 1030)
            $table->string('name');                          // Descripción / Nombre
            $table->text('description')->nullable();         // Medidas, detalles adicionales
            $table->unsignedInteger('rental_price');         // Valor arriendo unitario en CLP
            $table->unsignedInteger('stock')->default(0);    // Stock real disponible
            $table->string('image')->nullable();             // Ruta de la fotografía
            $table->boolean('is_active')->default(true);     // Visible / Oculto en catálogo
            $table->boolean('is_sale')->default(false);      // Diferenciar arriendos de ítems en venta directa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
