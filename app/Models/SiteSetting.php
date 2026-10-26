<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'site_tagline',
        'logo_path',
        'hero_badge',
        'hero_title',
        'hero_subtitle',
        'hero_bg_image',
        'company_address',
        'company_phone',
        'whatsapp_phone',
        'company_email',
        'business_hours',
        'company_legal_info',
        'map_embed_url',
        'instagram_url',
        'facebook_url',
        'tiktok_url',
        'shipping_title',
        'shipping_subtitle',
        'shipping_zones_info',
        'shipping_unload_rules',
        'shipping_setup_rules',
        'shipping_schedule_rules',
    ];

    protected static function booted(): void
    {
        static::saving(static function (SiteSetting $setting): void {
            if (! empty($setting->map_embed_url)) {
                $pattern = '/\Ahttps:\/\/(www\.google\.com\/maps\/embed|maps\.google\.com\/maps|www\.openstreetmap\.org\/export\/embed)\?[a-zA-Z0-9%\._\-=&!:]+\z/';
                if (! preg_match($pattern, (string) $setting->map_embed_url)) {
                    throw ValidationException::withMessages([
                        'map_embed_url' => ['La URL de inserción del mapa no es válida o contiene protocolos inseguros (Anti-SSRF / Anti-XSS).'],
                    ]);
                }
            }
        });

        static::saved(static function (): void {
            Cache::forget('site_settings.current');
        });

        static::updated(static function (): void {
            Cache::forget('site_settings.current');
        });

        static::deleted(static function (): void {
            Cache::forget('site_settings.current');
        });
    }

    /**
     * Retorna el número de WhatsApp limpio (solo dígitos) listo para enlaces directos wa.me/.
     */
    public function cleanWhatsappPhone(): string
    {
        $raw = (string) ($this->whatsapp_phone ?: $this->company_phone ?: config('services.whatsapp.phone', '56900000000'));
        $clean = preg_replace('/\D+/', '', $raw) ?? '';

        return $clean !== '' ? $clean : '56900000000';
    }

    /**
     * Retorna el teléfono formateado con prefijo + para enlaces tel:+.
     */
    public function cleanTelPhone(): string
    {
        $raw = (string) ($this->company_phone ?: $this->whatsapp_phone ?: '+56229876543');
        $clean = preg_replace('/\D+/', '', $raw) ?? '';

        return $clean !== '' ? "+{$clean}" : '+56229876543';
    }

    /**
     * Obtiene la configuración activa del sitio mediante singleton en caché permanente.
     */
    public static function current(): self
    {
        $cached = Cache::get('site_settings.current');

        if ($cached instanceof self) {
            return $cached;
        }

        Cache::forget('site_settings.current');

        return Cache::rememberForever('site_settings.current', static function (): self {
            return static::query()->first() ?? static::createDefault();
        });
    }

    /**
     * Genera y persiste la configuración por defecto si la base de datos está vacía.
     */
    public static function createDefault(): self
    {
        return static::query()->create([
            'site_name' => 'Arriendos & Eventos',
            'site_tagline' => 'Boutique de Banquetería, Vajilla & Menaje de Gala',
            'logo_path' => null,
            'hero_badge' => 'Catálogo Oficial • Temporada 2026',
            'hero_title' => 'Equipamiento y ambientación para ocasiones extraordinarias',
            'hero_subtitle' => 'Mobiliario de etiqueta, vajilla fina, cristalería y mantelería para bodas, galas y celebraciones corporativas. Celebra con distinción y logística puntual garantizada.',
            'hero_bg_image' => null,
            'company_address' => 'Av. El Parque 4120, Huechuraba, Región Metropolitana',
            'company_phone' => '+56 2 2987 6543',
            'whatsapp_phone' => (string) config('services.whatsapp.quote_phone', '56912345678'),
            'company_email' => 'contacto@arriendoseventos.cl',
            'business_hours' => 'Lunes a Viernes de 09:00 a 19:00 hrs. Sábados de 09:00 a 14:00 hrs (Atención y Despachos).',
            'company_legal_info' => 'Arriendos & Eventos SpA • RUT: 76.543.210-K • Servicios de Banquetería y Mobiliario de Alta Gama.',
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3330.822830386548!2d-70.66986682343828!3d-33.37435887342083!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9662c6f6634c44f3%3A0xbc4e5e4125b060d4!2sCiudad%20Empresarial!5e0!3m2!1ses!2scl!4v1700000000000!5m2!1ses!2scl',
            'instagram_url' => 'https://instagram.com',
            'facebook_url' => 'https://facebook.com',
            'tiktok_url' => 'https://tiktok.com',
            'shipping_title' => 'Políticas de Envíos, Montaje y Logística',
            'shipping_subtitle' => 'Coordinación profesional de transporte y retiro en terreno para que tu evento transcurra con total serenidad y puntualidad.',
            'shipping_zones_info' => 'Despachamos a todas las comunas del Gran Santiago y alrededores (Chicureo, Lampa, Colina, Pirque, Calera de Tango, Talagante). La tarifa de flete se cotiza según volumen del pedido y distancia desde nuestra bodega central.',
            'shipping_unload_rules' => 'La tarifa estándar contempla entrega y retiro a nivel de calle o primer piso, hasta un máximo de 20 metros desde la zona de estacionamiento del camión. Traslados por escaleras, montacargas o pendientes pronunciadas deben informarse al cotizar.',
            'shipping_setup_rules' => 'Carpas estructurales 10x10 y 10x13, toldos y equipamiento gastronómico a gas (hornos, mantenedores, freidoras) son instalados obligatoriamente por nuestro equipo técnico certificado para garantizar seguridad y sellado hermético.',
            'shipping_schedule_rules' => 'Para eventos en fines de semana, entregamos el día viernes en la tarde o sábado en la mañana, y retiramos el lunes hábil siguiente. Al momento de la entrega y del retiro, se realiza un conteo minucioso junto al responsable mediante guía de despacho firmada.',
        ]);
    }
}
