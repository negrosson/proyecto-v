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
        $admin = User::query()->firstOrNew(['email' => 'admin@arriendoseventos.cl']);
        if (! $admin->exists) {
            $admin->forceFill([
                'name' => 'Administrador General',
                'password' => Hash::make((string) env('SEED_ADMIN_PASSWORD', 'Admin2026!#Secure')),
                'is_admin' => true,
                'is_active' => true,
            ])->save();
        } else {
            $admin->forceFill(['is_admin' => true, 'is_active' => true])->save();
        }

        // 2. Catálogo Oficial de Productos y Categorías
        $this->call(ProductCatalogSeeder::class);

        // 3. Configuración del Sitio CMS y FAQs Reales
        $this->call(SiteContentSeeder::class);
    }
}
