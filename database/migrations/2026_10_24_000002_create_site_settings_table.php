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
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();

            // Identidad y Hero
            $table->string('site_name');
            $table->string('site_tagline');
            $table->string('logo_path')->nullable();
            $table->string('hero_badge');
            $table->string('hero_title');
            $table->text('hero_subtitle');
            $table->string('hero_bg_image')->nullable();

            // Datos de Contacto y Empresa
            $table->string('company_address');
            $table->string('company_phone');
            $table->string('whatsapp_phone');
            $table->string('company_email');
            $table->string('business_hours');
            $table->string('company_legal_info')->nullable();

            // Mapa y Redes Sociales
            $table->text('map_embed_url');
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('tiktok_url')->nullable();

            // Políticas de Envíos y Logística
            $table->string('shipping_title');
            $table->text('shipping_subtitle');
            $table->text('shipping_zones_info');
            $table->text('shipping_unload_rules');
            $table->text('shipping_setup_rules');
            $table->text('shipping_schedule_rules');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
