# Guía Técnica: Gestión y Configuración del Tamaño de Subida de Imágenes

Esta guía detalla de forma clara, directa y paso a paso cómo funciona el sistema de carga de imágenes en el catálogo y panel de administración, y cómo ajustar el tamaño máximo de subida en cualquier momento.

---

## 1. Configuración Actual del Proyecto

* **Tamaño Máximo Permitido:** **4 MB** (4.096 KB / 4.194.304 bytes).
* **Formatos Aceptados:** `JPEG (.jpg, .jpeg)`, `PNG (.png)` y `WEBP (.webp)`.
* **Procesamiento Automático:** Reconstrucción de píxeles en memoria, eliminación destructiva de metadatos EXIF (geolocalización de cámaras, etc.) y compresión automática a **WebP limpio de alta fidelidad**.
* **Ubicaciones en el Panel:**
  * **Fotografía de Productos:** Menú *"Catálogo & Stock > Productos & Menaje"* (`image`).
  * **Logo Oficial de la Empresa:** Menú *"Configuración > Ajustes del Sitio > Identidad & Hero"* (`logo_path`).
  * **Fondo del Hero Principal:** Menú *"Configuración > Ajustes del Sitio > Identidad & Hero"* (`hero_bg_image`).

---

## 2. Cómo Modificar el Tamaño Máximo (3 Capas de Control)

Para que un nuevo límite funcione correctamente, debe coincidir en las 3 capas que protegen la aplicación:

```
[ Navegador del Usuario ]
         │
         ▼
[ Capa 1: Servidor Web & PHP ] ──► php.ini (upload_max_filesize, post_max_size)
         │
         ▼
[ Capa 2: Panel Filament ]     ──► Resource (->maxSize(4096))
         │
         ▼
[ Capa 3: Sanitizador Binario ] ──► SafeImageSanitizer (MAX_BYTES = 4 MB)
```

---

### Capa 1: En el Formulario Filament (Validación en Pantalla)

En Filament, el método `->maxSize()` se define en **Kilobytes (KB)**:
* `2048` = 2 MB
* `4096` = 4 MB *(Configuración actual)*
* `8192` = 8 MB
* `10240` = 10 MB

#### Archivos a modificar:
1. **Productos:** [`app/Filament/Resources/ProductResource.php`](file:///c:/laragon/www/catalogo-arriendo/app/Filament/Resources/ProductResource.php#L152-L160)
   ```php
   Forms\Components\FileUpload::make('image')
       ->label('Fotografía de Producto')
       ->image()
       ->maxSize(4096) // <-- Cambiar aquí (en KB)
       ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
       ->helperText('Formatos permitidos: JPG, PNG, WebP. Tamaño máximo: 4 MB.')
       ->saveUploadedFileUsing(function (UploadedFile $file): string {
           return SafeImageSanitizer::sanitize($file, 'public', 'products');
       })
       ->columnSpanFull(),
   ```

2. **Logo y Fondo de Portada:** [`app/Filament/Resources/SiteSettingResource.php`](file:///c:/laragon/www/catalogo-arriendo/app/Filament/Resources/SiteSettingResource.php#L49-L74)
   * Modificar el `->maxSize(4096)` tanto en `logo_path` como en `hero_bg_image`.

---

### Capa 2: En el Sanitizador de Seguridad (Validación Binaria del Servidor)

El sanitizador [`app/Services/Security/SafeImageSanitizer.php`](file:///c:/laragon/www/catalogo-arriendo/app/Services/Security/SafeImageSanitizer.php#L20-L55) inspecciona el peso real de los bytes en el disco antes de procesar la imagen:

```php
final class SafeImageSanitizer
{
    // Cambiar la constante multiplicando los MB deseados por 1024 * 1024:
    public const MAX_BYTES = 4 * 1024 * 1024; // 4 MB (4.194.304 bytes)
    ...
```

Si deseas subirlo a 8 MB:
```php
public const MAX_BYTES = 8 * 1024 * 1024; // 8 MB
```

---

### Capa 3: En el Servidor PHP y Web Server (`php.ini` y Nginx/Apache)

Para que PHP permita transferir el archivo desde el navegador hasta Laravel:

#### En Laragon (Desarrollo Local):
1. Clic derecho en el icono de Laragon en la barra de tareas > **PHP** > **php.ini**.
2. Busca y ajusta los siguientes valores (deben ser mayores o iguales al límite de la app):
   ```ini
   upload_max_filesize = 10M
   post_max_size = 12M
   memory_limit = 256M
   ```
   *(En la instalación actual de Laragon ya viene con `2G`, por lo que soporta cualquier tamaño localmente sin problemas).*
3. Reinicia Apache desde la interfaz de Laragon.

#### En Servidores de Producción (Nginx / Apache / Laravel Cloud):
* **Nginx (`/etc/nginx/nginx.conf` o bloque del sitio):**
  ```nginx
  client_max_body_size 10M;
  ```
  *(Por defecto Nginx viene configurado en 1M; si no se sube este valor, el servidor rechazará la subida con error `413 Request Entity Too Large`).*
* **Apache (`.htaccess` o `httpd.conf`):**
  ```apache
  LimitRequestBody 10485760
  ```

---

## 3. Tabla de Referencia Rápida de Tamaños

| Tamaño Deseado | Valor en Filament (`->maxSize`) | Valor en PHP (`SafeImageSanitizer`) | Directiva `php.ini` |
| :--- | :--- | :--- | :--- |
| **2 MB** | `2048` | `2 * 1024 * 1024` | `upload_max_filesize = 4M` |
| **4 MB (Actual)** | `4096` | `4 * 1024 * 1024` | `upload_max_filesize = 8M` |
| **6 MB** | `6144` | `6 * 1024 * 1024` | `upload_max_filesize = 10M` |
| **8 MB** | `8192` | `8 * 1024 * 1024` | `upload_max_filesize = 12M` |
| **10 MB** | `10240` | `10 * 1024 * 1024` | `upload_max_filesize = 15M` |

---

## 4. ¿Qué ocurre internamente cuando se sube una foto?

1. **Recepción Temporal:** Livewire y Filament reciben el archivo y verifican que no exceda `maxSize(4096)`.
2. **Inspección de Firma (Magic Bytes):** `SafeImageSanitizer` verifica con `getimagesize()` que el archivo sea efectivamente una imagen real y no un ejecutable o script renombrado como `.jpg`.
3. **Escaneo Anti-Malware / Anti-Políglotas:** Analiza el flujo de bytes buscando secuencias peligrosas (`<?php`, `<script`, `eval(`, etc.) para bloquear shells y ataques RCE.
4. **Reconstrucción en Memoria con GD:** La imagen se deserializa píxel por píxel en un lienzo virgen, eliminando al 100% cualquier dato oculto o metadato EXIF.
5. **Conversión y Almacenamiento WebP:** Se comprime en formato `.webp` con calidad 85%, generando un nombre criptográfico ULID (ej: `01JBCDEF...webp`) dentro de `storage/app/public/products/`.
