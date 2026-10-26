LARAGON ARRANCA TODO , YA ESTA CONFIGURADO PARA EL PROYECTO, EN CASO DE QUERER USAR EL TERMINAL SE NECESITARIAN ESTOS COMANDOS:

### Iniciar servidor:

`php artisan serve`

### Instalar dependencias:

`npm install`

### Desarrollar:

`npm run dev`

### Migraciones:

`php artisan migrate`

### Seeders:


resulktados de test en security

`C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan test --filter=SecurityHardeningTest`

6 tests passed (6)
{"tool":"phpunit","result":"passed","tests":6,"passed":6,"assertions":28,"duration_ms":906} 

`C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan test`

28 tests passed (28)
{"tool":"phpunit","result":"passed","tests":28,"passed":28,"assertions":182,"duration_ms":1757} 

`php artisan db:seed`   



migraciones exitosas, se deben agregar los nuevos productos manualmente, revisar documentacion de prueba en [Documentos](file:///c:/laragon/www/catalogo-arriendo/Documentos/comandos/mm.md#para probar) para ver comandos de validacion

Opción 2: Desde la terminal de Laragon (La más cómoda)
Ve a la ventana de Laragon y haz clic en el botón "Terminal".
Como esa consola ya tiene PHP 8.3 activado por defecto, solo escribes:
bash
php artisan test --filter=SecurityHardeningTest
O todos los tests:
bash
php artisan test
