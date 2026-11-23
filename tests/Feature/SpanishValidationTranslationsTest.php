<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SpanishValidationTranslationsTest extends TestCase
{
    public function test_failed_livewire_file_upload_returns_spanish_message(): void
    {
        App::setLocale('es');

        $failedUpload = new UploadedFile(
            path: __FILE__,
            originalName: 'PORTADA.jpg',
            mimeType: 'image/jpeg',
            error: UPLOAD_ERR_INI_SIZE,
            test: false,
        );

        $validator = Validator::make(
            ['files' => [$failedUpload]],
            ['files.*' => ['required', 'file', 'max:12288']],
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'No se pudo subir el archivo. Verifica que no supere el tamaño máximo permitido por el servidor.',
            $validator->errors()->first('files.0'),
        );
    }

    public function test_core_spanish_translation_files_resolve_human_readable_messages(): void
    {
        App::setLocale('es');

        $this->assertSame(
            'Estas credenciales no coinciden con nuestros registros.',
            __('auth.failed'),
        );
        $this->assertSame(
            'El archivo imagen no debe pesar más de 4096 kilobytes.',
            __('validation.max.file', ['attribute' => 'imagen', 'max' => 4096]),
        );
        $this->assertSame('&laquo; Anterior', __('pagination.previous'));
        $this->assertSame('Tu contraseña ha sido restablecida.', __('passwords.reset'));
    }
}
