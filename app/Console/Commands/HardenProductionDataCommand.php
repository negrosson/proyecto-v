<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HardenProductionDataCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:harden-production
        {--remove-generic-admin : Elimina la cuenta genérica admin@admin.com si existe}
        {--deduplicate-products : Desactiva productos con nombre exactamente duplicado (conservando el de menor ID)}
        {--create-admin= : Crea o actualiza una cuenta administradora segura con el email indicado y genera una contraseña fuerte}';

    /**
     * @var string
     */
    protected $description = 'Aplica limpieza de datos y endurecimiento de cuentas/archivos antes o después de desplegar a producción.';

    public function handle(): int
    {
        $this->info('=== Hardening de Producción — VYS Productora de Eventos ===');

        // 1. Proteger storage/app/public contra ejecución de scripts PHP (anti-webshell cPanel)
        $storageHtaccessPath = storage_path('app/public/.htaccess');
        $htaccessContent = <<<'HTACCESS'
# Bloqueo anti-webshell: prohibir ejecución de scripts PHP dentro de uploads públicos
<FilesMatch "\.(php|phtml|php3|php4|php5|php7|php8|phar|cgi|pl|py|sh)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
    </IfModule>
</FilesMatch>
Options -Indexes -ExecCGI
HTACCESS;

        if (! is_dir(dirname($storageHtaccessPath))) {
            mkdir(dirname($storageHtaccessPath), 0775, true);
        }
        file_put_contents($storageHtaccessPath, $htaccessContent.PHP_EOL);
        $this->line('✓ Archivo anti-webshell asegurado en storage/app/public/.htaccess');

        // 2. Eliminar cuenta genérica admin@admin.com si se solicita
        if ($this->option('remove-generic-admin')) {
            $genericAdmin = User::query()->where('email', 'admin@admin.com')->first();
            if ($genericAdmin !== null) {
                $genericAdmin->delete();
                $this->warn('✓ Cuenta genérica peligrosa admin@admin.com eliminada.');
            } else {
                $this->line('• La cuenta admin@admin.com no existe (OK).');
            }
        }

        // 3. Crear o rotar cuenta administradora segura
        $adminEmail = $this->option('create-admin');
        if (is_string($adminEmail) && trim($adminEmail) !== '') {
            $email = strtolower(trim($adminEmail));
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error("Email inválido: {$email}");

                return self::FAILURE;
            }

            $generatedPassword = Str::password(20, letters: true, numbers: true, symbols: true, spaces: false);

            $user = User::query()->firstOrNew(['email' => $email]);
            $user->forceFill([
                'name' => $user->exists ? $user->name : 'Administrador VYS',
                'password' => Hash::make($generatedPassword),
                'is_admin' => true,
                'is_active' => true,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            $this->newLine();
            $this->info('✓ Cuenta administradora configurada con éxito:');
            $this->line("  Email:      {$email}");
            $this->line("  Contraseña: {$generatedPassword}");
            $this->warn('  Guarda esta contraseña en un gestor seguro y activa el 2FA desde el panel (/admin).');
            $this->newLine();
        }

        // 4. Detectar (y opcionalmente desactivar) productos duplicados exactos
        $duplicates = DB::table('products')
            ->select('name', DB::raw('COUNT(*) as total'))
            ->where('is_active', true)
            ->groupBy('name')
            ->having('total', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->line('• Cero productos activos con nombres duplicados (OK).');
        } else {
            foreach ($duplicates as $dup) {
                $items = Product::query()
                    ->where('name', $dup->name)
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->get();

                $this->warn("• Duplicado detectado: {$dup->name} (x{$items->count()})");

                if ($this->option('deduplicate-products')) {
                    $extras = $items->slice(1);
                    foreach ($extras as $extra) {
                        $extra->update(['is_active' => false]);
                        $this->line("  → Desactivado duplicado ID={$extra->id} (Cód: {$extra->code}). Conservado ID={$items->first()->id}.");
                    }
                }
            }
        }

        return self::SUCCESS;
    }
}
