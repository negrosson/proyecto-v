<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Media\PublicMediaCleaner;
use Illuminate\Console\Command;

class PruneOrphanMediaCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'media:prune-orphans {--dry-run : Solo muestra los archivos huérfanos sin eliminarlos}';

    /**
     * @var string
     */
    protected $description = 'Elimina imágenes huérfanas en storage/app/public y archivos temporales antiguos de Livewire.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $result = PublicMediaCleaner::pruneOrphans($dryRun);

        $deletedCount = count($result['deleted']);
        $freedMb = round($result['freed_bytes'] / 1024 / 1024, 2);

        if ($dryRun) {
            $this->info("[SIMULACIÓN] Se detectarían {$deletedCount} imagen(es) huérfana(s) y {$result['tmp_deleted']} temporal(es) ({$freedMb} MB liberables).");
        } else {
            $this->info("Limpieza completada: {$deletedCount} imagen(es) huérfana(s) y {$result['tmp_deleted']} temporal(es) eliminados ({$freedMb} MB liberados). {$result['kept']} archivo(s) activos conservados.");
        }

        foreach ($result['deleted'] as $path) {
            $this->line(" - {$path}");
        }

        return self::SUCCESS;
    }
}
