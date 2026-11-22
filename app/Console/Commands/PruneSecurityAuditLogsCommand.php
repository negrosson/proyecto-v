<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneSecurityAuditLogsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'security:prune-audit-logs {--days=180 : Conservar los últimos N días de auditoría forense} {--dry-run : Simular sin eliminar}';

    /**
     * @var string
     */
    protected $description = 'Aplica retención programada sobre security_audit_logs eliminando registros mayores a N días para evitar crecimiento ilimitado.';

    public function handle(): int
    {
        $days = max(30, (int) $this->option('days'));
        $dryRun = (bool) $this->option('dry-run');
        $cutoff = now()->subDays($days);

        $query = DB::table('security_audit_logs')->where('created_at', '<', $cutoff);
        $count = $query->count();

        if ($dryRun) {
            $this->info("[SIMULACIÓN] Se purgarían {$count} registro(s) de auditoría anteriores a {$cutoff->toDateTimeString()} (>{$days} días).");

            return self::SUCCESS;
        }

        $deleted = $query->delete();
        $this->info("Retención aplicada: {$deleted} registro(s) de auditoría anteriores a {$cutoff->toDateTimeString()} eliminados.");

        return self::SUCCESS;
    }
}
