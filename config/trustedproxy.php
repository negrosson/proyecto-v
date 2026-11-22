<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Proxies de Confianza (Anti-Spoofing de X-Forwarded-For)
    |--------------------------------------------------------------------------
    |
    | Por defecto es null (no confiar en cabeceras X-Forwarded-* de clientes).
    | En cPanel directo (Apache/LiteSpeed sin CDN) déjalo vacío.
    | Si usas Cloudflare u otro balanceador, define TRUSTED_PROXIES en .env con
    | los rangos IP/CIDR reales separados por coma.
    |
    */

    'proxies' => ($raw = env('TRUSTED_PROXIES')) !== null && trim((string) $raw) !== ''
        ? trim((string) $raw)
        : null,

];
