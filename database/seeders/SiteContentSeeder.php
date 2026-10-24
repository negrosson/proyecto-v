<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Configuración General del Sitio (CMS)
        SiteSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Arriendos & Eventos',
                'site_tagline' => 'Boutique de Banquetería, Vajilla & Menaje de Gala',
                'logo_path' => null,
                'hero_badge' => 'Catálogo Oficial • Temporada 2026',
                'hero_title' => 'Equipamiento y ambientación para ocasiones extraordinarias',
                'hero_subtitle' => 'Mobiliario de etiqueta, vajilla fina, cristalería y mantelería para bodas, galas y celebraciones corporativas. Celebra con distinción y logística puntual garantizada.',
                'hero_bg_image' => null,
                'company_address' => 'Av. El Parque 4120, Ciudad Empresarial, Huechuraba, Región Metropolitana',
                'company_phone' => '+56 2 2987 6543',
                'whatsapp_phone' => (string) config('services.whatsapp.quote_phone', '56912345678'),
                'company_email' => 'contacto@arriendoseventos.cl',
                'business_hours' => 'Lunes a Viernes de 09:00 a 19:00 hrs. Sábados de 09:00 a 14:00 hrs (Atención y Despachos).',
                'company_legal_info' => 'Arriendos & Eventos SpA • RUT: 76.543.210-K • Servicios de Banquetería y Mobiliario de Alta Gama.',
                'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3330.822830386548!2d-70.66986682343828!3d-33.37435887342083!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9662c6f6634c44f3%3A0xbc4e5e4125b060d4!2sCiudad%20Empresarial!5e0!3m2!1ses!2scl!4v1700000000000!5m2!1ses!2scl',
                'instagram_url' => 'https://instagram.com/arriendoseventos',
                'facebook_url' => 'https://facebook.com/arriendoseventos',
                'tiktok_url' => 'https://tiktok.com/@arriendoseventos',
                'shipping_title' => 'Políticas de Envíos, Montaje y Logística',
                'shipping_subtitle' => 'Coordinación profesional de transporte y retiro en terreno para que tu evento transcurra con total serenidad y puntualidad.',
                'shipping_zones_info' => 'Despachamos a todas las comunas del Gran Santiago y alrededores (Chicureo, Lampa, Colina, Pirque, Calera de Tango, Talagante). La tarifa de flete se cotiza según volumen del pedido y distancia desde nuestra bodega central.',
                'shipping_unload_rules' => 'La tarifa estándar contempla entrega y retiro a nivel de calle o primer piso, hasta un máximo de 20 metros desde la zona de estacionamiento del camión. Traslados por escaleras, montacargas o pendientes pronunciadas deben informarse al cotizar.',
                'shipping_setup_rules' => 'Carpas estructurales 10x10 y 10x13, toldos y equipamiento gastronómico a gas (hornos, mantenedores, freidoras) son instalados obligatoriamente por nuestro equipo técnico certificado para garantizar seguridad y sellado hermético.',
                'shipping_schedule_rules' => 'Para eventos en fines de semana, entregamos el día viernes en la tarde o sábado en la mañana, y retiramos el lunes hábil siguiente. Al momento de la entrega y del retiro, se realiza un conteo minucioso junto al responsable mediante guía de despacho firmada.',
            ]
        );

        // 2. Preguntas Frecuentes Reales del Rubro (FAQs)
        $faqs = [
            [
                'question' => '¿La vajilla y cristalería se devuelve limpia o sucia?',
                'answer' => 'Toda nuestra vajilla, cubiertos y cristalería se entrega higienizada, pulida y sellada en cajas. Al finalizar tu evento, NO necesitas lavarla: solo debes retirar los restos sólidos de comida y devolver cada pieza en sus jabas originales. El lavado industrial de alta temperatura corre por nuestra cuenta.',
                'category' => 'Vajilla y Lavado',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => '¿Cómo funciona la garantía en caso de roturas, pérdidas o daños?',
                'answer' => 'Al confirmar la reserva se establece una garantía reembolsable. Durante el retiro realizamos el conteo junto a ti; si existe alguna copa quebrada o prenda extraviada, únicamente se descuenta el valor unitario de reposición de ese ítem y se devuelve el saldo inmediatamente.',
                'category' => 'Garantías y Roturas',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => '¿Qué ocurre si los manteles se manchan con vino, comida o cera de vela?',
                'answer' => 'Las manchas habituales de banquetería (vino tinto, salsas, café o grasa de alimentos) están cubiertas por nuestro proceso de lavandería industrial. Sin embargo, quemaduras de cigarro, cortes en la tela o derrames de esperma de vela que inutilicen el mantel se consideran daño irreversible y se cobran a valor de reposición.',
                'category' => 'Vajilla y Lavado',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'question' => '¿El valor de arriendo es por horas o por el evento completo?',
                'answer' => 'Los valores publicados corresponden al arriendo por el evento (jornada completa), no por hora. Para eventos de día sábado, coordinamos la entrega entre viernes o sábado en la mañana y realizamos el retiro el día lunes durante la mañana sin recargo adicional por el domingo.',
                'category' => 'Reservas y Pagos',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'question' => '¿Con cuánta anticipación debo reservar y cómo se bloquea la fecha?',
                'answer' => 'En temporada alta (octubre a marzo) recomendamos reservar con al menos 15 días de anticipación. El stock y la fecha quedan bloqueados formalmente con el abono del 50% de la cotización, pagando el 50% restante contra entrega en el lugar del evento.',
                'category' => 'Reservas y Pagos',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'question' => '¿Qué pasa si llueve o hay mal clima y arrendé carpas o mobiliario?',
                'answer' => 'Nuestras carpas 10x10 y 10x13 son 100% impermeables y resistentes al viento. Si arrendaste mobiliario de madera rústica, puffs de cuerina o sillas Chiavari, estos deben ubicarse siempre bajo techo o carpa para proteger su acabado de la lluvia.',
                'category' => 'Despacho y Montaje',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faqData) {
            Faq::query()->updateOrCreate(
                ['question' => $faqData['question']],
                $faqData
            );
        }
    }
}
