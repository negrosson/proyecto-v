<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\SiteSettingResource\Pages\EditSiteSetting;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Security\SafeImageSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentImageUploadHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_replace_and_save_hero_image_without_500(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $setting = SiteSetting::current();

        $image = UploadedFile::fake()->image('portada.jpg', 1200, 800);

        Livewire::actingAs($admin)
            ->test(EditSiteSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm([
                'hero_bg_image' => $image,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $setting->refresh();
        $this->assertNotNull($setting->hero_bg_image);
        $this->assertStringStartsWith('site/', $setting->hero_bg_image);
        Storage::disk('public')->assertExists($setting->hero_bg_image);
    }

    public function test_removing_a_temporary_uploaded_file_in_filament_does_not_throw_type_error(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $setting = SiteSetting::current();

        $tempImage = UploadedFile::fake()->image('temporal.jpg', 800, 600);

        $component = Livewire::actingAs($admin)
            ->test(EditSiteSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm([
                'hero_bg_image' => $tempImage,
            ]);

        $uploadedState = $component->get('data.hero_bg_image');
        $this->assertIsArray($uploadedState);
        $fileKey = array_key_first($uploadedState);
        $this->assertNotNull($fileKey);

        $component
            ->call('deleteUploadedFile', 'data.hero_bg_image', (string) $fileKey)
            ->assertOk();

        $this->assertSame([], $component->get('data.hero_bg_image'));
    }

    public function test_sanitizer_rejection_in_filament_returns_validation_error_instead_of_500(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $setting = SiteSetting::current();

        $im = imagecreatetruecolor(40, 40);
        $tmpFile = tempnam(sys_get_temp_dir(), 'polyglot_filament_').'.jpg';
        imagejpeg($im, $tmpFile);
        imagedestroy($im);
        file_put_contents($tmpFile, "\n<?php echo 'pwn'; ?>", FILE_APPEND);

        try {
            $maliciousUpload = UploadedFile::fake()->createWithContent(
                'malicious.jpg',
                (string) file_get_contents($tmpFile),
            );

            Livewire::actingAs($admin)
                ->test(EditSiteSetting::class, ['record' => $setting->getRouteKey()])
                ->fillForm([
                    'hero_bg_image' => $maliciousUpload,
                ])
                ->call('save')
                ->assertHasFormErrors(['hero_bg_image']);
        } finally {
            if (file_exists($tmpFile)) {
                @unlink($tmpFile);
            }
        }
    }

    public function test_sanitizer_avoids_jpeg_binary_false_positive_for_short_tag_bytes_while_blocking_real_php_short_tags(): void
    {
        Storage::fake('public');

        // 1. Foto JPEG válida seguida de bytes aleatorios "<?=\xFF" (no es código PHP) -> debe sanitizarse sin falso positivo
        $im = imagecreatetruecolor(80, 60);
        $benignFile = tempnam(sys_get_temp_dir(), 'benign_bytes_').'.jpg';
        imagejpeg($im, $benignFile);
        imagedestroy($im);
        file_put_contents($benignFile, "<?=\xFF\x00", FILE_APPEND);

        try {
            $stored = SafeImageSanitizer::sanitize($benignFile, 'public', 'site');
            Storage::disk('public')->assertExists($stored);
        } finally {
            if (file_exists($benignFile)) {
                @unlink($benignFile);
            }
        }

        // 2. Foto JPEG con etiqueta corta PHP real -> debe ser rechazada
        $im2 = imagecreatetruecolor(80, 60);
        $phpShortTagFile = tempnam(sys_get_temp_dir(), 'short_tag_').'.jpg';
        imagejpeg($im2, $phpShortTagFile);
        imagedestroy($im2);
        file_put_contents($phpShortTagFile, "<?= system('id') ?>", FILE_APPEND);

        try {
            $this->expectException(InvalidArgumentException::class);
            SafeImageSanitizer::sanitize($phpShortTagFile, 'public', 'site');
        } finally {
            if (file_exists($phpShortTagFile)) {
                @unlink($phpShortTagFile);
            }
        }
    }
}
