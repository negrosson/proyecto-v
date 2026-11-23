<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que un producto pertenezca a una categoría (relación category()).
     */
    public function test_product_belongs_to_a_category(): void
    {
        $category = Category::factory()->create([
            'name' => 'Mobiliario',
            'slug' => 'mobiliario',
        ]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Silla Chiavari Dorada',
        ]);

        // Verificar el método de relación estructuralmente
        $relation = $product->category();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Category::class, $relation->getRelated());
        $this->assertSame('category_id', $relation->getForeignKeyName());
        $this->assertSame('id', $relation->getOwnerKeyName());

        // Verificar la resolución del modelo relacionado
        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertTrue($product->category->is($category));
        $this->assertSame($category->id, $product->category->id);
    }

    /**
     * Verifica que los campos clave se asignen correctamente por asignación masiva.
     */
    public function test_key_fields_are_mass_assignable(): void
    {
        $fillable = (new Product)->getFillable();

        // Verificar que los campos clave están explícitamente en $fillable
        $this->assertContains('name', $fillable);
        $this->assertContains('code', $fillable);
        $this->assertContains('rental_price', $fillable);
        $this->assertContains('stock', $fillable);
        $this->assertContains('is_active', $fillable);

        $attributes = [
            'name' => 'Mesa Redonda 1.80m',
            'code' => 2050,
            'rental_price' => 12000,
            'stock' => 25,
            'is_active' => true,
        ];

        $product = new Product($attributes);

        $this->assertSame('Mesa Redonda 1.80m', $product->name);
        $this->assertSame(2050, $product->code);
        $this->assertSame(12000, $product->rental_price);
        $this->assertSame(25, $product->stock);
        $this->assertTrue($product->is_active);
    }

    /**
     * Verifica que los campos clave asignados masivamente se guarden correctamente en base de datos.
     */
    public function test_key_fields_can_be_mass_assigned_and_persisted(): void
    {
        $category = Category::factory()->create();

        $attributes = [
            'category_id' => $category->id,
            'name' => 'Copa Vino Tinto Cabernet 450ml',
            'code' => 3040,
            'rental_price' => 1800,
            'stock' => 150,
            'is_active' => false,
        ];

        $product = Product::create($attributes);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'category_id' => $category->id,
            'name' => 'Copa Vino Tinto Cabernet 450ml',
            'code' => 3040,
            'rental_price' => 1800,
            'stock' => 150,
            'is_active' => false,
        ]);
    }

    /**
     * Verifica que el factory genere datos realistas para arriendos de eventos.
     */
    public function test_product_factory_generates_realistic_event_rental_data(): void
    {
        $product = Product::factory()->create();

        $this->assertIsInt($product->code);
        $this->assertGreaterThan(0, $product->code);
        $this->assertIsInt($product->rental_price);
        $this->assertGreaterThan(0, $product->rental_price);
        $this->assertIsInt($product->stock);
        $this->assertGreaterThanOrEqual(0, $product->stock);
        $this->assertNotEmpty($product->name);
        $this->assertNotNull($product->category_id);
    }

    /**
     * Verifica que las miniaturas comerciales de estudio se asignen según el tipo de artículo y categoría.
     */
    public function test_product_resolves_clean_hotel_fallback_thumbnails_by_item_type(): void
    {
        $vajilla = Category::factory()->create(['name' => 'Vajilla', 'slug' => 'vajilla']);
        $manteleria = Category::factory()->create(['name' => 'Mantelería', 'slug' => 'manteleria']);

        $plato = Product::factory()->create(['category_id' => $vajilla->id, 'name' => 'PLATO FONDO ECONOMICO', 'image' => null]);
        $platoBase = Product::factory()->create(['category_id' => $vajilla->id, 'name' => 'PLATO BASE RECTANGULAR O REDONDO', 'image' => null]);
        $cubiertoPlata = Product::factory()->create(['category_id' => $vajilla->id, 'name' => 'TENEDOR PRINCIPAL', 'image' => null]);
        $cubiertoDorado = Product::factory()->create(['category_id' => $vajilla->id, 'name' => 'TENEDOR PRINCIPAL DORADO', 'image' => null]);
        $copa = Product::factory()->create(['category_id' => $vajilla->id, 'name' => 'COPA VINO BLANCO', 'image' => null]);
        $vaso = Product::factory()->create(['category_id' => $vajilla->id, 'name' => 'VASO WISKY', 'image' => null]);
        $mantel = Product::factory()->create(['category_id' => $manteleria->id, 'name' => 'MANTEL REDONDO BLANCO', 'image' => null]);

        $this->assertNull($plato->image_url);
        $this->assertTrue($plato->usesFallbackImage());
        $this->assertTrue($plato->usesWhiteStudioBackground());
        $this->assertStringEndsWith('/images/products/default-platos.webp', $plato->display_image_url);
        $this->assertStringEndsWith('/images/products/default-plato-base.webp', $platoBase->display_image_url);
        $this->assertStringEndsWith('/images/products/default-cubiertos.webp', $cubiertoPlata->display_image_url);
        $this->assertStringEndsWith('/images/products/articulo-cubiertos-dorados.webp', $cubiertoDorado->display_image_url);
        $this->assertFalse($cubiertoDorado->usesWhiteStudioBackground());
        $this->assertStringEndsWith('/images/products/default-copas.webp', $copa->display_image_url);
        $this->assertStringEndsWith('/images/products/articulo-vaso-vidrio.webp', $vaso->display_image_url);
        $this->assertFalse($vaso->usesWhiteStudioBackground());
        $this->assertStringEndsWith('/images/products/default-manteleria.webp', $mantel->display_image_url);
    }
}
