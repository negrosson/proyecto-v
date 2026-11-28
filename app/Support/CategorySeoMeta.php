<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Category;

final class CategorySeoMeta
{
    /**
     * Retorna metadatos y contenido editorial único optimizado para SEO local en Chile
     * por cada categoría del catálogo (evitando páginas duplicadas en Google).
     *
     * @return array{
     *     title: string,
     *     description: string,
     *     h1: string,
     *     h1_accent: string,
     *     lead: string,
     *     highlights: list<string>,
     *     seo_heading: string,
     *     seo_paragraph: string,
     * }
     */
    public static function for(Category $category, string $siteBrand): array
    {
        return match ($category->slug) {
            'mobiliario' => [
                'title' => "Arriendo de Mobiliario para Eventos en Santiago — Sillas y Mesas | {$siteBrand}",
                'description' => "Arriendo de sillas Chiavari doradas, sillas Tiffany blancas, mesas redondas de banquete, mesones y mobiliario para matrimonios y eventos corporativos en Santiago y regiones. Cotiza por WhatsApp con {$siteBrand}.",
                'h1' => 'Arriendo de Mobiliario para Eventos',
                'h1_accent' => 'en Santiago y Regiones',
                'lead' => 'Sillas Chiavari doradas y blancas, sillas plegables, mesas redondas de banquete, mesones rectangulares y mobiliario de etiqueta para bodas, galas y celebraciones corporativas.',
                'highlights' => [
                    'Stock real para eventos de 20 a +300 invitados',
                    'Despacho y retiro en Las Condes, Providencia, Vitacura, Chicureo y todo Santiago',
                    'Reserva directa y rápida por WhatsApp',
                ],
                'seo_heading' => 'Mobiliario de etiqueta con despacho puntual en la Región Metropolitana',
                'seo_paragraph' => 'Nuestro servicio de arriendo de mobiliario para eventos en Santiago cubre matrimonios, fiestas de empresa, graduaciones y cenas de gala. Cada silla y mesa se entrega revisada, limpia y lista para el montaje en comunas como Las Condes, Vitacura, Lo Barnechea, Providencia, Ñuñoa, Huechuraba, Chicureo y regiones cercanas.',
            ],
            'vajilla' => [
                'title' => "Arriendo de Vajilla, Platos, Copas y Cubiertos para Eventos en Santiago | {$siteBrand}",
                'description' => "Arriendo de vajilla fina de porcelana, bajoplatos dorados, copas de vino y espumante, vasos y cubiertos dorados o plateados en Santiago. Lavado y sanitización 100% incluidos. Cotiza online con {$siteBrand}.",
                'h1' => 'Arriendo de Vajilla y Cristalería',
                'h1_accent' => 'con Lavado Incluido',
                'lead' => 'Platos de fondo, entrada y postre en porcelana blanca, bajoplatos dorados de gala, copas de vino, agua y brindis, vasos de coctelería y cuchillería fina — nos devuelves todo sin lavar.',
                'highlights' => [
                    'Lavado y sanitización profesional 100% incluidos',
                    'Cuchillería dorada y plateada, copas talladas y porcelana fina',
                    'Despacho coordinado en Santiago y regiones',
                ],
                'seo_heading' => 'Arriendo de vajillas y cristalería para matrimonios, banquetes y cenas',
                'seo_paragraph' => 'En nuestro catálogo de arriendo de vajilla en Santiago encuentras juegos completos para cenas de tres tiempos, cócteles y celebraciones corporativas. Todo el menaje se entrega sanitizado en cajas de transporte seguras y el retiro incluye lavado profesional para que disfrutes tu evento sin preocupaciones.',
            ],
            'manteleria' => [
                'title' => "Arriendo de Mantelería, Servilletas y Caminos de Mesa en Santiago | {$siteBrand}",
                'description' => "Arriendo de manteles redondos y rectangulares, carpetas, caminos de mesa y servilletas de género para matrimonios y banquetes en Santiago. Lavado y planchado incluidos. Cotiza con {$siteBrand}.",
                'h1' => 'Arriendo de Mantelería para Eventos',
                'h1_accent' => 'en Santiago',
                'lead' => 'Manteles redondos y rectangulares en lino, blanco, negro y tonos champaña, carpetas, caminos de mesa estampados y servilletas de tela listas para vestir tus mesas de gala.',
                'highlights' => [
                    'Entrega planchada e impecable lista para montar',
                    'Lavado profesional incluido al retirar',
                    'Amplia variedad de medidas y colores para banquetería',
                ],
                'seo_heading' => 'Mantelería fina para matrimonios, cenas de gala y eventos corporativos',
                'seo_paragraph' => 'Ofrecemos arriendo de mantelería en Santiago adaptada a mesas redondas de 8 a 10 personas y mesones rectangulares de banquete. Trabajamos con productoras, banqueteras y clientes particulares en toda la Región Metropolitana con entregas programadas antes de cada montaje.',
            ],
            'equipamiento-cocina' => [
                'title' => "Arriendo de Equipamiento de Cocina y Banquetería Industrial en Santiago | {$siteBrand}",
                'description' => "Arriendo de hornos industriales, chafing dishes, arroceras, jugueras, termos y equipamiento gastronómico para banquetes y catering en Santiago y regiones. Cotiza online con {$siteBrand}.",
                'h1' => 'Arriendo de Equipamiento de Cocina',
                'h1_accent' => 'y Banquetería',
                'lead' => 'Hornos industriales, mantenedores de calor (chafing dishes), arroceras de gran capacidad, bandejas de horno, dispensadores y equipos profesionales para catering y banqueteras.',
                'highlights' => [
                    'Equipos gastronómicos revisados antes de cada evento',
                    'Ideal para banqueteras, matrimonios y eventos corporativos',
                    'Despacho coordinado en Santiago y alrededores',
                ],
                'seo_heading' => 'Equipamiento gastronómico confiable para banquetería y eventos masivos',
                'seo_paragraph' => 'Respaldamos la operación de cocina de tu evento con arriendo de equipamiento gastronómico en Santiago: hornos, chafing dishes, bandejas, jugueras y utensilios de servicio en acero inoxidable, disponibles para eventos sociales, corporativos y producciones en terreno.',
            ],
            'bar-decoracion' => [
                'title' => "Arriendo de Accesorios de Bar, Arcos y Decoración para Eventos en Santiago | {$siteBrand}",
                'description' => "Arriendo de arcos de novios, hieleras, cocteleras, bandejas de garzón, lámparas y accesorios de bar y decoración para matrimonios y fiestas en Santiago. Cotiza directo con {$siteBrand}.",
                'h1' => 'Arriendo de Bar y Decoración',
                'h1_accent' => 'para Eventos',
                'lead' => 'Arcos de ceremonia para novios, sets de coctelería, hieleras, bandejas de servicio, lámparas y piezas decorativas para elevar la puesta en escena de matrimonios, cócteles y galas.',
                'highlights' => [
                    'Piezas protagonistas para ceremonias y zonas de bar',
                    'Combinables con nuestro mobiliario, vajilla y mantelería',
                    'Cotización rápida por WhatsApp en minutos',
                ],
                'seo_heading' => 'Accesorios de bar y ambientación para bodas y celebraciones en Santiago',
                'seo_paragraph' => 'Complementa tu montaje con nuestro arriendo de accesorios de bar y decoración en Santiago: arcos de madera y estructura para ceremonias, elementos de coctelería profesional y detalles de ambientación pensados para matrimonios, recepciones y lanzamientos de marca.',
            ],
            default => [
                'title' => "Arriendo de {$category->name} para Eventos en Santiago | {$siteBrand}",
                'description' => "Arriendo de {$category->name} para bodas, matrimonios, galas y eventos corporativos en Santiago y regiones. Cotiza online directo por WhatsApp con {$siteBrand}.",
                'h1' => "Arriendo de {$category->name}",
                'h1_accent' => 'para Eventos en Santiago',
                'lead' => "Explora nuestra selección oficial de {$category->name} en arriendo para matrimonios, banquetes y celebraciones corporativas con despacho coordinado en Santiago y regiones.",
                'highlights' => [
                    'Stock disponible y verificado en bodega',
                    'Despacho y retiro coordinado en Santiago y regiones',
                    'Cotización directa por WhatsApp',
                ],
                'seo_heading' => "Servicio de arriendo de {$category->name} en Santiago y Regiones",
                'seo_paragraph' => "En {$siteBrand} ofrecemos arriendo de {$category->name} con estándares de banquetería profesional para bodas, celebraciones familiares y eventos de empresa en toda la Región Metropolitana.",
            ],
        };
    }
}
