<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $categories = [
                'vajilla' => Category::firstOrCreate(
                    ['slug' => 'vajilla'],
                    ['name' => 'Vajilla']
                ),
                'manteleria' => Category::firstOrCreate(
                    ['slug' => 'manteleria'],
                    ['name' => 'Mantelería']
                ),
                'mobiliario' => Category::firstOrCreate(
                    ['slug' => 'mobiliario'],
                    ['name' => 'Mobiliario']
                ),
                'equipamiento-cocina' => Category::firstOrCreate(
                    ['slug' => 'equipamiento-cocina'],
                    ['name' => 'Equipamiento Cocina']
                ),
                'bar-decoracion' => Category::firstOrCreate(
                    ['slug' => 'bar-decoracion'],
                    ['name' => 'Accesorios Bar & Decoración']
                ),
            ];

            $products = $this->getCatalogProducts();

            foreach ($products as $item) {
                $category = $categories[$item['category']];

                Product::updateOrCreate(
                    ['code' => $item['code']],
                    [
                        'category_id' => $category->id,
                        'name' => $item['name'],
                        'description' => $item['description'] ?? null,
                        'rental_price' => $item['rental_price'],
                        'stock' => $item['stock'],
                        'image' => $item['image'] ?? null,
                        'is_active' => $item['is_active'] ?? true,
                        'is_sale' => $item['is_sale'] ?? false,
                        'is_featured' => $item['is_featured'] ?? false,
                    ]
                );
            }
        });
    }

    /**
     * Catálogo completo extraído del inventario PDF con correcciones de columnas y tipos.
     *
     * @return array<int, array{code: int, name: string, stock: int, rental_price: int, category: string, is_sale?: bool, is_active?: bool, description?: string}>
     */
    private function getCatalogProducts(): array
    {
        return [
            // ==========================================
            // PÁGINA 1: Vajilla (Códigos 10 al 390)
            // ==========================================
            ['code' => 10, 'name' => 'PLATO FONDO ECONOMICO', 'stock' => 500, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 20, 'name' => 'PLATO CUADRADO 23 cm', 'stock' => 700, 'rental_price' => 150, 'category' => 'vajilla'],
            ['code' => 30, 'name' => 'PLATO BASE RECTANGULAR O REDONDO', 'stock' => 300, 'rental_price' => 150, 'category' => 'vajilla'],
            ['code' => 40, 'name' => 'PLATO ENTRADA', 'stock' => 500, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 50, 'name' => 'PLATO DE PAN CUADRADO', 'stock' => 300, 'rental_price' => 80, 'category' => 'vajilla'],
            ['code' => 60, 'name' => 'PLATO LENGUA LOZA ENSALADAS', 'stock' => 100, 'rental_price' => 300, 'category' => 'vajilla'],
            ['code' => 61, 'name' => 'PLATO PARA COCTEL REDONDO O RECTANGULAR', 'stock' => 20, 'rental_price' => 300, 'category' => 'vajilla'],
            ['code' => 70, 'name' => 'PLATO BANDEJA PARA COCTEL METALICA', 'stock' => 50, 'rental_price' => 350, 'category' => 'vajilla'],
            ['code' => 80, 'name' => 'PLATO ENSALADA RECTANGULAR', 'stock' => 5, 'rental_price' => 500, 'category' => 'vajilla'],
            ['code' => 90, 'name' => 'ENSALADERA ONDAS LOZA O CRISTAL', 'stock' => 40, 'rental_price' => 500, 'category' => 'vajilla'],
            ['code' => 100, 'name' => 'ARGOLLA PORTA SERVILLETA DORADO Y PLATEADO', 'stock' => 300, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 120, 'name' => 'PLATO PLAQUET PLATA', 'stock' => 40, 'rental_price' => 500, 'category' => 'vajilla'],
            ['code' => 130, 'name' => 'CUCHARA CEVICHE LOZA', 'stock' => 1000, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 140, 'name' => 'PLATO PAN', 'stock' => 400, 'rental_price' => 80, 'category' => 'vajilla'],
            ['code' => 141, 'name' => 'POCILLO LOZA MINI INDIVIDUAL', 'stock' => 500, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 142, 'name' => 'POCILLO GREDA REDONDO INDIVIDUAL', 'stock' => 100, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 150, 'name' => 'PLATO TORTA', 'stock' => 500, 'rental_price' => 80, 'category' => 'vajilla'],
            ['code' => 160, 'name' => 'CUCHARA SOPERA', 'stock' => 200, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 170, 'name' => 'TENEDOR PRINCIPAL', 'stock' => 700, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 180, 'name' => 'CUCHILLO PRINCIPAL', 'stock' => 700, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 190, 'name' => 'TENEDOR ENTRADA', 'stock' => 700, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 200, 'name' => 'CUCHILLO ENTRADA', 'stock' => 700, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 210, 'name' => 'CUCHARA CHICA TE', 'stock' => 10, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 211, 'name' => 'TENEDOR PRINCIPAL DORADO', 'stock' => 200, 'rental_price' => 200, 'category' => 'vajilla', 'is_featured' => true, 'description' => 'Cubierto de acero inoxidable con baño dorado satinado de alta durabilidad para montajes de lujo.'],
            ['code' => 212, 'name' => 'CUCHILLO PRINCIPAL DORADO', 'stock' => 200, 'rental_price' => 200, 'category' => 'vajilla', 'is_featured' => true, 'description' => 'Cuchillo de mesa con acabado oro brillante para banquetes y bodas de etiqueta.'],
            ['code' => 213, 'name' => 'TENEDOR ENTRADA DORADO', 'stock' => 200, 'rental_price' => 200, 'category' => 'vajilla'],
            ['code' => 214, 'name' => 'CUCHILLO ENTRADA DORADO', 'stock' => 200, 'rental_price' => 200, 'category' => 'vajilla'],
            ['code' => 215, 'name' => 'CUCHARA CHICA TE DORADO', 'stock' => 200, 'rental_price' => 200, 'category' => 'vajilla'],
            ['code' => 220, 'name' => 'COPA VINO', 'stock' => 700, 'rental_price' => 120, 'category' => 'vajilla', 'is_featured' => true, 'image' => 'images/products/copa-vino.jpg', 'description' => 'Cristalería fina de transparencia cristalina con tallo estilizado para servicio de vino tinto o blanco.'],
            ['code' => 230, 'name' => 'COPA BRINDIS', 'stock' => 700, 'rental_price' => 120, 'category' => 'vajilla', 'is_featured' => true, 'image' => 'images/products/copa-vino.jpg', 'description' => 'Copa tipo flauta estilizada para brindis con espumante o champaña en celebraciones especiales.'],
            ['code' => 240, 'name' => 'COPA AGUA', 'stock' => 500, 'rental_price' => 120, 'category' => 'vajilla', 'image' => 'images/products/copa-vino.jpg'],
            ['code' => 250, 'name' => 'COPA TEQUILA', 'stock' => 100, 'rental_price' => 200, 'category' => 'vajilla'],
            ['code' => 260, 'name' => 'COPA APEROL', 'stock' => 200, 'rental_price' => 150, 'category' => 'vajilla'],
            ['code' => 270, 'name' => 'COPA CEVICHE', 'stock' => 300, 'rental_price' => 200, 'category' => 'vajilla'],
            ['code' => 280, 'name' => 'COPA MARTINI', 'stock' => 130, 'rental_price' => 200, 'category' => 'vajilla'],
            ['code' => 290, 'name' => 'VASO LARGO', 'stock' => 1176, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 291, 'name' => 'COPA HELADO', 'stock' => 125, 'rental_price' => 200, 'category' => 'vajilla'],
            ['code' => 292, 'name' => 'COPA VINO BLANCO', 'stock' => 300, 'rental_price' => 120, 'category' => 'vajilla'],
            ['code' => 300, 'name' => 'VASO CERVECERO', 'stock' => 100, 'rental_price' => 150, 'category' => 'vajilla'],
            ['code' => 310, 'name' => 'VASO WISKY', 'stock' => 500, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 320, 'name' => 'VASO CHICO', 'stock' => 500, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 330, 'name' => 'CUCHARA CHICA PARA SALSA', 'stock' => 110, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 331, 'name' => 'VASO VIDRIO SHOT PARA POSTRES', 'stock' => 100, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 332, 'name' => 'CUCHARA POSTRE SHOT', 'stock' => 100, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 340, 'name' => 'CUCHARA CHICA DE CAFÉ', 'stock' => 300, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 350, 'name' => 'TENEDOR TORTA', 'stock' => 500, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 360, 'name' => 'POCILLO POSTRE LOZA', 'stock' => 300, 'rental_price' => 200, 'category' => 'vajilla'],
            ['code' => 370, 'name' => 'CUCHARA POSTRE', 'stock' => 400, 'rental_price' => 100, 'category' => 'vajilla'],
            ['code' => 371, 'name' => 'REMEKEN', 'stock' => 300, 'rental_price' => 200, 'category' => 'vajilla'],
            ['code' => 380, 'name' => 'ALCUZA COMPLETA', 'stock' => 20, 'rental_price' => 500, 'category' => 'vajilla'],
            ['code' => 390, 'name' => 'SERVILLETERO METALICOS', 'stock' => 40, 'rental_price' => 350, 'category' => 'vajilla'],

            // ==========================================
            // PÁGINA 2: Vajilla, Accesorios Bar & Mantelería (Códigos 400 al 850)
            // ==========================================
            ['code' => 400, 'name' => 'JARRO JUGO 1.5 LITRO', 'stock' => 100, 'rental_price' => 800, 'category' => 'vajilla'],
            ['code' => 401, 'name' => 'PORTA SALSA LOZA', 'stock' => 10, 'rental_price' => 350, 'category' => 'vajilla'],
            ['code' => 402, 'name' => 'PORTA SALSA METALICO', 'stock' => 20, 'rental_price' => 200, 'category' => 'vajilla'],
            ['code' => 410, 'name' => 'DISPENSADOR DE LIQUIDOS 3 LITROS CON LLAVE', 'stock' => 4, 'rental_price' => 3000, 'category' => 'equipamiento-cocina'],
            ['code' => 420, 'name' => 'POCILLO SALSA O PEBRE GREDA', 'stock' => 30, 'rental_price' => 400, 'category' => 'vajilla'],
            ['code' => 430, 'name' => 'POCILLO SALSA O PEBRE LOZA', 'stock' => 50, 'rental_price' => 400, 'category' => 'vajilla'],
            ['code' => 440, 'name' => 'BANDEJA GARZÓN', 'stock' => 40, 'rental_price' => 1000, 'category' => 'bar-decoracion'],
            ['code' => 450, 'name' => 'PANERA', 'stock' => 20, 'rental_price' => 350, 'category' => 'vajilla'],
            ['code' => 460, 'name' => 'HIELERA DE CRISTAL CON PINZA', 'stock' => 20, 'rental_price' => 990, 'category' => 'bar-decoracion'],
            ['code' => 470, 'name' => 'HIELERA METÁLICA PARA BAR', 'stock' => 10, 'rental_price' => 2000, 'category' => 'bar-decoracion'],
            ['code' => 480, 'name' => 'HIELERA METÁLICA CON PINZA', 'stock' => 10, 'rental_price' => 2000, 'category' => 'bar-decoracion'],
            ['code' => 490, 'name' => 'TAZA CON PLATILLO CAFÉ CHICA', 'stock' => 300, 'rental_price' => 250, 'category' => 'vajilla'],
            ['code' => 491, 'name' => 'HIELERA BAR ACRILICA', 'stock' => 50, 'rental_price' => 1000, 'category' => 'bar-decoracion'],
            ['code' => 500, 'name' => 'TAZA CONSOME', 'stock' => 200, 'rental_price' => 350, 'category' => 'vajilla'],
            ['code' => 510, 'name' => 'PALETA PARA ENSALADAS', 'stock' => 26, 'rental_price' => 300, 'category' => 'vajilla'],
            ['code' => 520, 'name' => 'PALETA DE TORTA', 'stock' => 20, 'rental_price' => 300, 'category' => 'vajilla'],
            ['code' => 530, 'name' => 'PINZA METALICAS', 'stock' => 20, 'rental_price' => 500, 'category' => 'vajilla'],
            ['code' => 540, 'name' => 'SALEROS VIDRIO O LOZA', 'stock' => 30, 'rental_price' => 250, 'category' => 'vajilla'],
            ['code' => 541, 'name' => 'PIMENTERO', 'stock' => 20, 'rental_price' => 250, 'category' => 'vajilla'],
            ['code' => 550, 'name' => 'TAZA TE CON PLATILLO', 'stock' => 600, 'rental_price' => 250, 'category' => 'vajilla'],
            ['code' => 560, 'name' => 'BOTELLA ALIÑO', 'stock' => 20, 'rental_price' => 350, 'category' => 'vajilla'],
            ['code' => 570, 'name' => 'AZUCARERO VIDRIO O LOZA', 'stock' => 20, 'rental_price' => 250, 'category' => 'vajilla'],
            ['code' => 580, 'name' => 'CAFETERO LOZA', 'stock' => 4, 'rental_price' => 350, 'category' => 'vajilla'],
            ['code' => 590, 'name' => 'PORTA TORTA LOZA / VIDRIO / METALICO', 'stock' => 2, 'rental_price' => 1500, 'category' => 'vajilla'],
            ['code' => 600, 'name' => 'PESCERA DE VIDRIO', 'stock' => 12, 'rental_price' => 1000, 'category' => 'bar-decoracion'],
            ['code' => 610, 'name' => 'BANDEJA PLAQUET CON TAPA DE VIDRIO', 'stock' => 10, 'rental_price' => 2000, 'category' => 'bar-decoracion'],
            ['code' => 620, 'name' => 'FLORERO CIRCULAR GRANDE', 'stock' => 10, 'rental_price' => 3000, 'category' => 'bar-decoracion'],
            ['code' => 630, 'name' => 'PORTA VELA CIRCULAR DORADO', 'stock' => 10, 'rental_price' => 1000, 'category' => 'bar-decoracion'],
            ['code' => 640, 'name' => 'CENICERO LOZA', 'stock' => 100, 'rental_price' => 350, 'category' => 'bar-decoracion'],
            ['code' => 641, 'name' => 'CENICERO METALICOS', 'stock' => 20, 'rental_price' => 300, 'category' => 'bar-decoracion'],
            ['code' => 650, 'name' => 'FLORERO VINTAGE', 'stock' => 13, 'rental_price' => 1500, 'category' => 'bar-decoracion'],
            ['code' => 660, 'name' => 'FLORES DORADAS', 'stock' => 6, 'rental_price' => 5000, 'category' => 'bar-decoracion'],
            ['code' => 670, 'name' => 'FLORERO PIPETA 80 CM ALTO', 'stock' => 6, 'rental_price' => 5000, 'category' => 'bar-decoracion'],
            ['code' => 680, 'name' => 'FLORERO 20 CM CUADRADO', 'stock' => 10, 'rental_price' => 1500, 'category' => 'bar-decoracion'],
            ['code' => 690, 'name' => 'FLOREROS SILUETA DE 15 CM', 'stock' => 10, 'rental_price' => 1000, 'category' => 'bar-decoracion'],
            ['code' => 700, 'name' => 'PLATO MADERA PARA ARREGLO', 'stock' => 12, 'rental_price' => 500, 'category' => 'bar-decoracion'],
            ['code' => 701, 'name' => 'COCTELERA', 'stock' => 1, 'rental_price' => 2000, 'category' => 'bar-decoracion'],
            ['code' => 705, 'name' => 'DECANTADOR DE VINO', 'stock' => 10, 'rental_price' => 1000, 'category' => 'bar-decoracion'],
            ['code' => 706, 'name' => 'PLATO DORADO PLASTICO', 'stock' => 400, 'rental_price' => 550, 'category' => 'vajilla'],
            ['code' => 707, 'name' => 'JARRO METALICO AGUA CALIENTE', 'stock' => 10, 'rental_price' => 800, 'category' => 'vajilla'],
            ['code' => 805, 'name' => 'MANTEL REDONDO O RECTANGULAR BLANCO O NEGRO', 'stock' => 22, 'rental_price' => 3000, 'category' => 'manteleria', 'is_featured' => true, 'description' => 'Mantelería pesada de alta calidad con caída elegante para mesas de gala, banquetes y cenas formales.'],
            ['code' => 810, 'name' => 'MANTEL REDONDO O RECTANGULAR COLORES Y ESTAMPADO', 'stock' => 10, 'rental_price' => 4000, 'category' => 'manteleria'],
            ['code' => 815, 'name' => 'MANTEL RECTANGULAR 3X1.5 VARIEDAD DE COLOR', 'stock' => 29, 'rental_price' => 2500, 'category' => 'manteleria'],
            ['code' => 820, 'name' => 'CUBRE SILLAS NEGRAS', 'stock' => 30, 'rental_price' => 500, 'category' => 'manteleria'],
            ['code' => 825, 'name' => 'CUBRE SILLA BLANCA', 'stock' => 425, 'rental_price' => 500, 'category' => 'manteleria'],
            ['code' => 826, 'name' => 'CUBRE SILLAS AZUL REY', 'stock' => 25, 'rental_price' => 500, 'category' => 'manteleria'],
            ['code' => 830, 'name' => 'LAZO VARIEDAD EN COLORES', 'stock' => 1670, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 835, 'name' => 'LAZO ARPILLERA', 'stock' => 110, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 840, 'name' => 'LAZO ROJO', 'stock' => 110, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 845, 'name' => 'LAZO VERDE PISTACHO', 'stock' => 100, 'rental_price' => 100, 'category' => 'manteleria'],
            ['code' => 850, 'name' => 'LAZO AZUL REY', 'stock' => 130, 'rental_price' => 200, 'category' => 'manteleria'],

            // ==========================================
            // PÁGINA 3: Mantelería y Mobiliario (Códigos 855 al 1150)
            // ==========================================
            ['code' => 855, 'name' => 'LAZO NEGRO', 'stock' => 50, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 860, 'name' => 'LAZO CAFÉ CLARO', 'stock' => 50, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 865, 'name' => 'LAZO CAFÉ OSCURO', 'stock' => 50, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 870, 'name' => 'LAZO LILA', 'stock' => 100, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 871, 'name' => 'LAZO FUCSIA', 'stock' => 100, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 875, 'name' => 'LAZO MORADO', 'stock' => 50, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 880, 'name' => 'LAZO CORAL', 'stock' => 120, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 885, 'name' => 'LAZO DAMASCO', 'stock' => 100, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 890, 'name' => 'LAZO AMARILLO', 'stock' => 100, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 895, 'name' => 'LAZO DORADO', 'stock' => 120, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 900, 'name' => 'LAZO GRIS', 'stock' => 40, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 905, 'name' => 'LAZO ROSADO', 'stock' => 100, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 910, 'name' => 'LAZO CELESTE', 'stock' => 100, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 915, 'name' => 'LAZO BURDEO', 'stock' => 130, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 920, 'name' => 'LAZO NARANJO', 'stock' => 50, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 925, 'name' => 'LAZO TURQUESA', 'stock' => 50, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 930, 'name' => 'LAZO AZUL', 'stock' => 20, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 935, 'name' => 'CARPETAS 1.5X1.5 VARIEDAD DE COLOR', 'stock' => 100, 'rental_price' => 2000, 'category' => 'manteleria'],
            ['code' => 940, 'name' => 'CAMINO 3X35 CM VARIEDAD DE COLOR', 'stock' => 100, 'rental_price' => 1500, 'category' => 'manteleria'],
            ['code' => 945, 'name' => 'CAMINO 3 X 45 ESTAMPADO O RUSTICOS', 'stock' => 100, 'rental_price' => 3000, 'category' => 'manteleria'],
            ['code' => 950, 'name' => 'SERVILLETAS GENERO VARIEDAD DE COLOR', 'stock' => 200, 'rental_price' => 200, 'category' => 'manteleria'],
            ['code' => 955, 'name' => 'MANDIL NEGRO', 'stock' => 8, 'rental_price' => 2000, 'category' => 'manteleria'],
            ['code' => 960, 'name' => 'FALDON COLORES 4 METRO', 'stock' => 1, 'rental_price' => 4500, 'category' => 'manteleria'],
            ['code' => 965, 'name' => 'CAMINO DE FLORES DE 2 METRO', 'stock' => 5, 'rental_price' => 5000, 'category' => 'bar-decoracion'],
            ['code' => 966, 'name' => 'ARREGLO FLORAL REDONDO', 'stock' => 20, 'rental_price' => 3000, 'category' => 'bar-decoracion'],
            ['code' => 970, 'name' => 'FALDON BLANCO 3 METROS', 'stock' => 2, 'rental_price' => 4000, 'category' => 'manteleria'],
            ['code' => 975, 'name' => 'CARPETAS 1.5X1.5 VARIEDAD ESTAMPADAS', 'stock' => 50, 'rental_price' => 3000, 'category' => 'manteleria'],
            ['code' => 976, 'name' => 'PORTA SERVILLETAS ARGOLLA DORADA O PLATEADA', 'stock' => 500, 'rental_price' => 100, 'category' => 'manteleria'],
            ['code' => 977, 'name' => 'INDIVIDUAL YUTE', 'stock' => 100, 'rental_price' => 300, 'category' => 'manteleria'],
            ['code' => 1000, 'name' => 'MESA REDONDA 10 PERSONAS 1.50 D.', 'stock' => 42, 'rental_price' => 3500, 'category' => 'mobiliario', 'is_featured' => true, 'image' => 'images/products/mesa-banquete.jpg', 'description' => 'Mesa redonda de 1.50 metros de diámetro con capacidad para 8 a 10 comensales. Estructura de alta estabilidad con patas plegables de acero reforzado.'],
            ['code' => 1001, 'name' => 'MESA RECTANGULAR 10 PERSONAS 2.40X75', 'stock' => 50, 'rental_price' => 3500, 'category' => 'mobiliario', 'image' => 'images/products/mesa-banquete.jpg'],
            ['code' => 1002, 'name' => 'MESA RUSTICA MADERA', 'stock' => 10, 'rental_price' => 15000, 'category' => 'mobiliario', 'is_featured' => true, 'image' => 'images/products/mesa-banquete.jpg', 'description' => 'Mesa tablón de madera rústica natural tratada, ideal para bodas estilo campestre o recepciones al aire libre sin necesidad de mantel.'],
            ['code' => 1010, 'name' => 'MESAS RECTANGULAR 8 PERSONAS 1.80 X 75 PLEGABLE', 'stock' => 16, 'rental_price' => 2500, 'category' => 'mobiliario', 'image' => 'images/products/mesa-banquete.jpg'],
            ['code' => 1020, 'name' => 'SILLA PLEGABLES NEGRAS', 'stock' => 507, 'rental_price' => 500, 'category' => 'mobiliario', 'image' => 'images/products/silla-plegable-blanca.jpg'],
            ['code' => 1021, 'name' => 'SILLAS PLEGABLE BLANCA', 'stock' => 80, 'rental_price' => 1300, 'category' => 'mobiliario', 'is_featured' => true, 'image' => 'images/products/silla-plegable-blanca.jpg', 'description' => 'Silla plegable de resina blanca con asiento acolchado impermeable. Óptima para ceremonias civiles, jardines y cócteles elegantes.'],
            ['code' => 1030, 'name' => 'SILLA CHIAVARI BLANCA', 'stock' => 400, 'rental_price' => 1800, 'category' => 'mobiliario', 'is_featured' => true, 'image' => 'images/products/silla-chiavari-dorada.jpg', 'description' => 'Silla Chiavari en tono blanco perlado de alta resistencia con cojín desmontable. Aporta luminosidad, sofisticación y estilo clásico a ceremonias de matrimonio y eventos al aire libre.'],
            ['code' => 1031, 'name' => 'SILLAS CHIAVARI DORADA', 'stock' => 200, 'rental_price' => 1500, 'category' => 'mobiliario', 'is_featured' => true, 'image' => 'images/products/silla-chiavari-dorada.jpg', 'description' => 'Elegante silla Chiavari con acabado dorado premium y cojín blanco acolchado. Es la elección predilecta para bodas de gala, banquetes y recepciones de etiqueta.'],
            ['code' => 1032, 'name' => 'SILLA CHIAVARI BLANCA O DORADA S/C', 'stock' => 400, 'rental_price' => 1500, 'category' => 'mobiliario', 'image' => 'images/products/silla-chiavari-dorada.jpg'],
            ['code' => 1040, 'name' => 'MESA BLANCA NOVIOS', 'stock' => 1, 'rental_price' => 20000, 'category' => 'mobiliario', 'is_featured' => true, 'image' => 'images/products/mesa-banquete.jpg', 'description' => 'Mesa nupcial presidencial laqueada en blanco impoluto para novios. Diseño exclusivo que resalta en el salón principal.'],
            ['code' => 1050, 'name' => 'ATRIL PORTA LETRERO MADERA', 'stock' => 3, 'rental_price' => 1500, 'category' => 'bar-decoracion'],
            ['code' => 1060, 'name' => 'MESA DE COCTEL DE APOYO', 'stock' => 11, 'rental_price' => 2500, 'category' => 'mobiliario', 'is_featured' => true, 'image' => 'images/products/mesa-banquete.jpg', 'description' => 'Mesa alta para cóctel y recepción de bienvenida. Estructura ligera y plegable, perfecta para espacios de socialización y bienvenida de invitados.'],
            ['code' => 1070, 'name' => 'CASCADA CHOCOLATE', 'stock' => 3, 'rental_price' => 10000, 'category' => 'equipamiento-cocina'],
            ['code' => 1080, 'name' => 'MESA DE APOYO CROMADA Y BASE ACRILICA', 'stock' => 10, 'rental_price' => 13000, 'category' => 'mobiliario'],
            ['code' => 1090, 'name' => 'PUFF BLANCO DE CUERINA 1 PERSONA', 'stock' => 4, 'rental_price' => 5000, 'category' => 'mobiliario'],
            ['code' => 1100, 'name' => 'PUFF BLANCO DE CUERINA 2 PERSONAS', 'stock' => 2, 'rental_price' => 15000, 'category' => 'mobiliario'],
            ['code' => 1110, 'name' => 'TERMO HERVIDOR DE 10 LITRO', 'stock' => 7, 'rental_price' => 10000, 'category' => 'equipamiento-cocina'],
            ['code' => 1111, 'name' => 'TERMO HERVIDOR 40 LITRO', 'stock' => 3, 'rental_price' => 35000, 'category' => 'equipamiento-cocina'],
            ['code' => 1120, 'name' => 'HORNO ELECTRICO', 'stock' => 2, 'rental_price' => 25000, 'category' => 'equipamiento-cocina'],
            ['code' => 1130, 'name' => 'FOGON DOBLE SIN GAS', 'stock' => 1, 'rental_price' => 20000, 'category' => 'equipamiento-cocina'],
            ['code' => 1140, 'name' => 'ALFOMBRA ROJA 10 METRO', 'stock' => 1, 'rental_price' => 20000, 'category' => 'mobiliario'],
            ['code' => 1150, 'name' => 'DISPENSADOR DE AGUA CON LLAVE 5 LITRO', 'stock' => 2, 'rental_price' => 3000, 'category' => 'equipamiento-cocina'],

            // ==========================================
            // PÁGINA 4: Equipamiento Cocina y Mobiliario Mayor (Códigos 1160 al 1291)
            // ==========================================
            ['code' => 1160, 'name' => 'MAQUINA PICADORA DE HIELO', 'stock' => 1, 'rental_price' => 10000, 'category' => 'equipamiento-cocina'],
            ['code' => 1170, 'name' => 'PORTA CERVEZA 100 UN. 350 CC', 'stock' => 1, 'rental_price' => 20000, 'category' => 'bar-decoracion'],
            ['code' => 1180, 'name' => 'PORTA POSTRE JAULA DORADA', 'stock' => 2, 'rental_price' => 5000, 'category' => 'bar-decoracion'],
            ['code' => 1190, 'name' => 'FOGON DOBLE CON GAS', 'stock' => 2, 'rental_price' => 45000, 'category' => 'equipamiento-cocina'],
            ['code' => 1200, 'name' => 'CALEFACTOR A GAS CON GAS 11K.', 'stock' => 10, 'rental_price' => 45000, 'category' => 'bar-decoracion', 'is_featured' => true, 'image' => 'images/products/calefactor-gas.jpg', 'description' => 'Calefactor exterior tipo seta/pirámide a gas licuado de 11 kg incluido. Proporciona confort térmico radiante y un ambiente acogedor para terrazas y carpas en eventos nocturnos.'],
            ['code' => 1210, 'name' => 'MAQUINA HUMO', 'stock' => 1, 'rental_price' => 25000, 'category' => 'bar-decoracion'],
            ['code' => 1220, 'name' => 'LUCES FOCO', 'stock' => 10, 'rental_price' => 10000, 'category' => 'bar-decoracion'],
            ['code' => 1230, 'name' => 'ARCO DE NOVIO MADERA TUL Y FLOR ARTIFICIAL', 'stock' => 1, 'rental_price' => 40000, 'category' => 'bar-decoracion'],
            ['code' => 1240, 'name' => 'ARCO DE NOVIO MADERA TUL Y FLOR NATURAL', 'stock' => 1, 'rental_price' => 100000, 'category' => 'bar-decoracion'],
            ['code' => 1250, 'name' => 'CORTINAJE SEGUN MEDIDAS', 'stock' => 1, 'rental_price' => 200000, 'category' => 'bar-decoracion'],
            ['code' => 1260, 'name' => 'HORNOS INDUSTRIAL CON 2 BANDEJA', 'stock' => 2, 'rental_price' => 30000, 'category' => 'equipamiento-cocina'],
            ['code' => 1261, 'name' => 'HORNO INDUSTRIAL CON 2 BANDEJA CON GAS', 'stock' => 2, 'rental_price' => 50000, 'category' => 'equipamiento-cocina'],
            ['code' => 1262, 'name' => 'FREIDORA SIN GAS', 'stock' => 1, 'rental_price' => 35000, 'category' => 'equipamiento-cocina'],
            ['code' => 1263, 'name' => 'CHAFFER', 'stock' => 5, 'rental_price' => 10000, 'category' => 'equipamiento-cocina'],
            ['code' => 1264, 'name' => 'PERCOLADORA 16 LITRO', 'stock' => 2, 'rental_price' => 20000, 'category' => 'equipamiento-cocina'],
            ['code' => 1265, 'name' => 'TABLA PICAR MADERA PARRILLA 40x50', 'stock' => 10, 'rental_price' => 3000, 'category' => 'equipamiento-cocina'],
            ['code' => 1266, 'name' => 'HIELERA 400 LITRO', 'stock' => 4, 'rental_price' => 50000, 'category' => 'equipamiento-cocina'],
            ['code' => 1267, 'name' => 'COOLER 1 PUERTA', 'stock' => 5, 'rental_price' => 50000, 'category' => 'equipamiento-cocina'],
            ['code' => 1268, 'name' => 'TRIO MESA DORADA', 'stock' => 2, 'rental_price' => 30000, 'category' => 'mobiliario'],
            ['code' => 1269, 'name' => 'FONDO GRANDE', 'stock' => 6, 'rental_price' => 8000, 'category' => 'equipamiento-cocina'],
            ['code' => 1270, 'name' => 'ARROCERA 15 KILO', 'stock' => 2, 'rental_price' => 8000, 'category' => 'equipamiento-cocina'],
            ['code' => 1271, 'name' => 'PASTO ARTIFICIAL SEGUN MEDIDA', 'stock' => 1, 'rental_price' => 200000, 'category' => 'bar-decoracion'],
            ['code' => 1272, 'name' => 'CAMBRO 25 LITROS', 'stock' => 3, 'rental_price' => 10000, 'category' => 'equipamiento-cocina'],
            ['code' => 1273, 'name' => 'HORNOS ELECTRICO INDUSTRIAL', 'stock' => 3, 'rental_price' => 30000, 'category' => 'equipamiento-cocina'],
            ['code' => 1274, 'name' => 'BARTENDER', 'stock' => 1, 'rental_price' => 60000, 'category' => 'bar-decoracion'],
            ['code' => 1275, 'name' => 'PARRILLA', 'stock' => 1, 'rental_price' => 40000, 'category' => 'equipamiento-cocina'],
            ['code' => 1276, 'name' => 'MESON BAR CAFE', 'stock' => 1, 'rental_price' => 30000, 'category' => 'mobiliario'],
            ['code' => 1277, 'name' => 'COJIN PARA SILLAS BLANCO', 'stock' => 300, 'rental_price' => 300, 'category' => 'manteleria'],
            ['code' => 1278, 'name' => 'ALFOMBRA ROJA 10 METRO', 'stock' => 1, 'rental_price' => 24000, 'category' => 'mobiliario'],
            ['code' => 1279, 'name' => 'MESON BAR BLANCO', 'stock' => 1, 'rental_price' => 45000, 'category' => 'mobiliario'],
            ['code' => 1280, 'name' => 'PEDESTAL ROMANO BLANCO', 'stock' => 2, 'rental_price' => 5000, 'category' => 'bar-decoracion'],
            ['code' => 1281, 'name' => 'ESTUFA EXTERIOR CON GAS', 'stock' => 4, 'rental_price' => 45000, 'category' => 'bar-decoracion'],
            ['code' => 1282, 'name' => 'JUGUERA', 'stock' => 2, 'rental_price' => 20000, 'category' => 'equipamiento-cocina'],
            ['code' => 1283, 'name' => 'PARAFINA SOLIDA', 'stock' => 100, 'rental_price' => 2000, 'category' => 'bar-decoracion'],
            ['code' => 1284, 'name' => 'BANDEJA HORNO GRANDE BUDINERA', 'stock' => 1, 'rental_price' => 5000, 'category' => 'equipamiento-cocina'],
            ['code' => 1285, 'name' => 'CARPA IMPERMEABLE 10X10', 'stock' => 2, 'rental_price' => 400000, 'category' => 'mobiliario'],
            ['code' => 1286, 'name' => 'ENCHUFE', 'stock' => 5, 'rental_price' => 5000, 'category' => 'bar-decoracion'],
            ['code' => 1287, 'name' => 'ESPEJO DE CUERPO ENTERO 30x120', 'stock' => 4, 'rental_price' => 15000, 'category' => 'mobiliario'],
            ['code' => 1288, 'name' => 'BASURERO', 'stock' => 4, 'rental_price' => 10000, 'category' => 'bar-decoracion'],
            ['code' => 1289, 'name' => 'VENTA CARPA IMPERMEABLE 10x13 COLOR NEGRA', 'stock' => 1, 'rental_price' => 4000000, 'is_sale' => true, 'category' => 'mobiliario'],
            ['code' => 1290, 'name' => 'SEGUNDO DIA ARRIENDO', 'stock' => 1, 'rental_price' => 60000, 'category' => 'bar-decoracion'],
            ['code' => 1291, 'name' => 'PERCOLADORA 10 LITRO', 'stock' => 1, 'rental_price' => 15000, 'category' => 'equipamiento-cocina'],
        ];
    }
}
