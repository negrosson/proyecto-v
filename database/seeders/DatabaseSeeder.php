<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Usuario Administrador Inicial para Filament con acceso OWASP A01
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@arriendoseventos.cl'],
            [
                'name' => 'Administrador General',
                'password' => Hash::make('Admin2026!#Secure'),
                'is_admin' => true,
            ]
        );
        $admin->update(['is_admin' => true]);

        // Asegurar que si existe un usuario previo (ej: id=1), también sea admin
        User::query()->where('id', 1)->update(['is_admin' => true]);

        // 2. Catálogo Oficial de Productos y Categorías
        $this->call(ProductCatalogSeeder::class);

        // 3. Configuración del Sitio CMS y FAQs Reales
        $this->call(SiteContentSeeder::class);
    }
}
