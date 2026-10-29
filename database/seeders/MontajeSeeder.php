<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Montaje;
use Illuminate\Database\Seeder;

class MontajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $montajes = [
            [
                'title' => 'T E N O   2 0 2 3',
                'slug' => 'teno-2023',
                'client' => 'Agrozzi',
                'type' => 'Evento Empresa',
                'year' => '2023',
                'location' => 'Teno, Curicó',
                'capacity' => 'Montaje completo para 250 personas',
                'summary' => 'Evento para Agrozzi en Teno, realizado el 2023. Montaje completo para 250 personas con ambientación integral.',
                'description' => 'Montaje integral de banquetería corporativa a gran escala. Se implementaron mesas redondas con mantelería base color crudo, caminos de yute natural, centros de mesa florales silvestres y sillas Tiffany blancas. El servicio incluyó vajilla completa de cerámica, cubiertos de acero inoxidable y cristalería fina de vino y agua.',
                'cover_image' => 'images/montajes/teno-mesa-principal.jpg',
                'gallery' => [
                    'images/montajes/teno-mesa-principal.jpg',
                    'images/montajes/teno-salon-250p.jpg',
                    'images/montajes/montaje-teno.png',
                ],
                'items_used' => [
                    '250x Sillas Tiffany Blancas',
                    '25x Mesas Redondas de Banquete (10 personas)',
                    '25x Manteles Redondos Base Cruda',
                    '25x Caminos de Mesa en Yute Rústico',
                    '250x Copas de Vino & Copas de Agua',
                    'Vajilla Completa de 3 Tiempos',
                ],
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Gala Nocturna & Ambientación Dorada',
                'slug' => 'gala-nocturna',
                'client' => 'Corporativo Santiago',
                'type' => 'Gala & Cena de Fin de Año',
                'year' => '2024',
                'location' => 'Las Condes, Santiago',
                'capacity' => 'Cena de Gala para 180 invitados',
                'summary' => 'Ambientación de gala con iluminación cálida, centros de rosas rojas y bajoplatos dorados.',
                'description' => 'Producción de alta etiqueta diseñada para una celebración de fin de año corporativa. Mesas redondas con bajoplatos de oro viejo, copas talladas de cristal, servilletas de lino dobladas a mano y rosas rojas importadas como punto focal. Zona perimetral equipada con barras iluminadas y lounges de descanso.',
                'cover_image' => 'images/montajes/gala-nocturna-dorada.jpg',
                'gallery' => [
                    'images/montajes/gala-nocturna-dorada.jpg',
                    'images/montajes/montajes-galeria.png',
                ],
                'items_used' => [
                    '180x Sillas Tiffany Doradas',
                    '18x Mesas Circulares Amplias',
                    '180x Bajoplatos Dorados de Gala',
                    'Cristalería Bar & Coctelería de Lujo',
                    'Barras Modulares Retroiluminadas',
                ],
                'sort_order' => 2,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Banquete Moderno Black & White',
                'slug' => 'black-and-white',
                'client' => 'Institucional Metropolitano',
                'type' => 'Evento Corporativo',
                'year' => '2024',
                'location' => 'Huechuraba, Región Metropolitana',
                'capacity' => '320 comensales simultáneos',
                'summary' => 'Contraste vanguardista con manteles negros profundos y sillas blancas Tiffany.',
                'description' => 'Un concepto visual audaz y distinguido montado en un centro de eventos empresarial. La combinación de manteles negros azabache con la pureza de las sillas Tiffany blancas y los arreglos florales de temporada brindaron un impacto estético memorable a los más de 300 invitados.',
                'cover_image' => 'images/montajes/banquete-black-and-white.jpg',
                'gallery' => [
                    'images/montajes/banquete-black-and-white.jpg',
                    'images/montajes/montajes-galeria.png',
                ],
                'items_used' => [
                    '320x Sillas Blancas para Eventos',
                    '32x Manteles Circulares Negros',
                    '32x Caminos de Mesa Estructurados',
                    'Vajilla Cuadrada Moderna & Platería',
                    'Calefactores de Ambiente a Gas',
                ],
                'sort_order' => 3,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Boda Chic en Pabellón Acristalado',
                'slug' => 'pabellon-campestre',
                'client' => 'Matrimonio Privado',
                'type' => 'Matrimonio & Boda',
                'year' => '2025',
                'location' => 'Chicureo, Colina',
                'capacity' => '200 invitados en salón campestre',
                'summary' => 'Celebración campestre elegante con lámparas de mimbre colgantes y vajilla clásica.',
                'description' => 'Montaje soñado para un matrimonio de tarde en salón con ventanales de piso a cielo. Las mesas redondas vistieron manteles blancos inmaculados con detalles rústicos elegantes, copas de cristal labrado y una cálida integración de luminarias de mimbre que aportaron calidez al atardecer.',
                'cover_image' => 'images/montajes/matrimonio-campestre-pabellon.jpg',
                'gallery' => [
                    'images/montajes/matrimonio-campestre-pabellon.jpg',
                    'images/montajes/montajes-galeria.png',
                ],
                'items_used' => [
                    '200x Sillas de Madera & Tiffany Blancas',
                    'Lámparas Colgantes de Mimbre',
                    'Vajilla de Porcelana con Borde Plateado',
                    'Copas de Espumante & Vino Reserva',
                    'Mesas Auxiliares de Degustación',
                ],
                'sort_order' => 4,
                'is_featured' => true,
                'is_active' => true,
            ],
        ];

        foreach ($montajes as $data) {
            Montaje::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
