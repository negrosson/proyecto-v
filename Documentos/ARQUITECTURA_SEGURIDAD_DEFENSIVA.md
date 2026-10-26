# Manual de Arquitectura de Seguridad Defensiva (Zero-Trust) & Forense
**Proyecto:** Catálogo & Sistema de Arriendos para Eventos  
**Stack:** Laravel 11 • FilamentPHP v3 • Livewire v3 • PHP 8.3 (`declare(strict_types=1);`) • MySQL 8  
**Fecha de Implementación:** Octubre 2026  
**Clasificación:** Grado Bancario / Forense / OWASP Top 10 Hardened  

---

## 1. Resumen Ejecutivo y Enfoque Zero-Trust

La presente arquitectura implementa un modelo de seguridad **Zero-Trust** ("nunca confiar, siempre verificar") diseñado para mitigar vectores avanzados de intrusión identificados en pruebas de penetración (Pentesting) y normativas internacionales (OWASP, NIST SP 800-63B).

El sistema protege todas las capas de la aplicación:
1. **Criptografía de Acceso:** Algoritmo TOTP RFC 6238 puro sin dependencias externas vulnerables.
2. **Integridad de Sesión:** Neutralización de ataques de repetición (Replay Attacks) y condiciones de carrera (Race Conditions).
3. **Resistencia de Componentes:** Inmunidad contra manipulación de estado Livewire (`wire:snapshot`) mediante Burp Suite.
4. **Contención de Carga de Archivos:** Sanitización binaria y re-codificación destructiva contra archivos políglotas (RCE).
5. **Trazabilidad Inmutable:** Registro forense con firma HMAC-SHA256 y encadenamiento criptográfico (Hash Chaining).
6. **Defensa en Profundidad en Base de Datos:** Restricciones `CHECK` a nivel de motor MySQL y protección contra escalada de privilegios.
7. **Cabeceras HTTP de Aislamiento:** Mitigación de Clickjacking, XSS y MIME Confusion.

---

## 2. Desglose Técnico de los Módulos de Seguridad

### Módulo 1: Motor Criptográfico TOTP (RFC 6238) Anti-Replay y Anti-Race Condition
* **Archivo Servicio:** [`app/Services/Security/TotpService.php`](file:///c:/laragon/www/catalogo-arriendo/app/Services/Security/TotpService.php)
* **Migración:** [`database/migrations/2026_10_25_230001_add_hardened_mfa_to_users_table.php`](file:///c:/laragon/www/catalogo-arriendo/database/migrations/2026_10_25_230001_add_hardened_mfa_to_users_table.php)

#### 1. Algoritmo Criptográfico Puro (RFC 6238)
* Genera semillas Base32 seguras de 160 bits (32 caracteres) mediante `random_bytes()` del kernel del sistema operativo.
* Convierte el tiempo Unix actual en un bloque temporal o "slice" (`floor(time() / 30)`).
* Empaqueta el bloque en formato binario Big-Endian de 64 bits (`pack('N*', 0) . pack('N*', $slice)`).
* Aplica `hash_hmac('sha1', $timeBytes, $secretKey, true)`.
* Extrae dinámicamente un número de 31 bits usando el offset del último byte (`ord($hash[19]) & 0x0F`) y calcula el módulo `1.000.000` rellenando con ceros a la izquierda para producir un código exacto de 6 dígitos.

#### 2. Mitigación de Replay Attacks (`last_totp_slice`)
* En una implementación estándar de TOTP, el código es válido durante toda la ventana de 30 segundos. Un atacante con un sniffer o Man-in-the-Middle podría reutilizar el código dentro de ese intervalo.
* **Solución defensiva:** Cada vez que un código es validado con éxito, se persiste en la base de datos el slice utilizado en la columna `last_totp_slice`. Si se recibe cualquier código cuyo slice sea `<=` al último registrado, el sistema lo **rechaza de inmediato**, imposibilitando que un código sea utilizado dos veces.

#### 3. Mitigación de Race Conditions (`lockForUpdate`)
* Los atacantes utilizan herramientas de concurrencia extrema (como *Burp Suite Turbo Intruder*) enviando cientos de solicitudes simultáneas en milisegundos con el mismo código antes de que la base de datos guarde el estado.
* **Solución defensiva:** Toda la validación y consumo se ejecuta dentro de una transacción atómica `DB::transaction()` con bloqueo pesimista:
  ```php
  $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->first();
  ```
  Esto bloquea la fila del usuario a nivel de motor MySQL (`SELECT ... FOR UPDATE`), encolando las peticiones concurrentes y forzando a que la segunda petición encuentre el `last_totp_slice` ya actualizado y sea rechazada.

#### 4. Códigos de Recuperación en Tiempo Constante
* Se generan 8 códigos alfanuméricos únicos almacenados con cifrado autenticado AES-256 (`'encrypted:array'`).
* Su validación en `consumeRecoveryCode()` evalúa cada código con `hash_equals()` sin hacer un `break` temprano, garantizando tiempo de respuesta idéntico para neutralizar ataques de canal lateral basados en tiempo (Timing Attacks).

#### 5. Bloqueo Automático (Circuit Breaker)
* Tras 5 intentos fallidos consecutivos de segundo factor, el middleware y el controlador aplican un bloqueo forzoso de 15 minutos en la columna `mfa_locked_until`, cierran la sesión y emiten una alerta crítica en el log forense.

---

### Módulo 2: Blindaje de Livewire v3 / Filament v3 contra Manipulación de Payloads
* **Página de Reto MFA:** [`app/Filament/Pages/TwoFactorChallengePage.php`](file:///c:/laragon/www/catalogo-arriendo/app/Filament/Pages/TwoFactorChallengePage.php)
* **Página de Gestión 2FA:** [`app/Filament/Pages/TwoFactorSecurityPage.php`](file:///c:/laragon/www/catalogo-arriendo/app/Filament/Pages/TwoFactorSecurityPage.php)
* **Middleware Guardián:** [`app/Http/Middleware/EnforceAdminMfaMiddleware.php`](file:///c:/laragon/www/catalogo-arriendo/app/Http/Middleware/EnforceAdminMfaMiddleware.php)

1. **Atributo `#[Locked]` de Livewire v3:**
   * En Livewire v3, los atacantes pueden interceptar las peticiones HTTP y modificar el payload JSON de `wire:snapshot` para cambiar IDs o propiedades internas.
   * Todas las propiedades sensibles (ej. `#[Locked] public ?int $userId`) están selladas criptográficamente por Livewire. Si el atacante altera el valor en Burp Suite, Livewire detecta que la firma del snapshot no coincide y bloquea la petición arrojando un error 403.
2. **Modo Sudo (Elevación de Privilegios):**
   * Cualquier acción crítica (activar 2FA, desactivar 2FA o regenerar códigos de emergencia) exige ingresar la contraseña actual del usuario mediante la regla de validación nativa `current_password:web`.
3. **Regeneración de Sesión contra Session Fixation:**
   * Al superar el reto 2FA, se ejecuta obligatoriamente `$request->session()->regenerate()`, destruyendo el ID de sesión anterior y vinculando el marcador de validación a la IP de origen (`session('mfa_verified_ip') === $request->ip()`).

---

### Módulo 3: Sanitizador Binario de Imágenes Anti-Políglotas y Destrucción EXIF
* **Servicio:** [`app/Services/Security/SafeImageSanitizer.php`](file:///c:/laragon/www/catalogo-arriendo/app/Services/Security/SafeImageSanitizer.php)
* **Integración:** [`ProductResource.php`](file:///c:/laragon/www/catalogo-arriendo/app/Filament/Resources/ProductResource.php) y [`SiteSettingResource.php`](file:///c:/laragon/www/catalogo-arriendo/app/Filament/Resources/SiteSettingResource.php)
* **Contenedor Apache:** `storage/app/public/.htaccess`

1. **Inspección de Firmas Binarias:**
   * No confía en la extensión provista por el usuario ni en la cabecera `Content-Type`. Valida mediante `getimagesize()` que el archivo sea estrictamente `IMAGETYPE_JPEG`, `IMAGETYPE_PNG` o `IMAGETYPE_WEBP`.
2. **Escaneo de Inyecciones de Código:**
   * Escanea el flujo binario crudo del archivo y descarta de inmediato cualquier carga que contenga secuencias ejecutables como `<?php`, `<?=`, `<script`, `<!DOCTYPE`, `eval(`, `base64_decode`, etc.
3. **Re-codificación Destructiva con GD (Exterminación de EXIF / EOF Payloads):**
   * Un atacante puede esconder código PHP dentro de los metadatos EXIF de una foto real o pegarlo al final del archivo (EOF injection).
   * El servicio carga la matriz de píxeles pura en memoria con `imagecreatefromstring()`, crea un lienzo virgen y recompila la imagen desde cero en formato `.webp` sobrescribiéndola con un nombre aleatorio criptográfico generado con `Str::ulid()`. Este proceso destruye el 100% de los metadatos EXIF y los bytes residuales.
4. **Contención a Nivel de Servidor Web:**
   * El archivo `.htaccess` en el almacenamiento público desactiva el listado de directorios y la ejecución de CGI/PHP (`Options -Indexes -ExecCGI` y `RemoveHandler .php`).

---

### Módulo 4: Registro Forense Inmutable con Firma HMAC y Hash Chaining
* **Modelo:** [`app/Models/SecurityAuditLog.php`](file:///c:/laragon/www/catalogo-arriendo/app/Models/SecurityAuditLog.php)
* **Migración:** [`database/migrations/2026_10_25_230002_create_security_audit_logs_table.php`](file:///c:/laragon/www/catalogo-arriendo/database/migrations/2026_10_25_230002_create_security_audit_logs_table.php)
* **Recurso Administrativo:** [`app/Filament/Resources/SecurityAuditLogResource.php`](file:///c:/laragon/www/catalogo-arriendo/app/Filament/Resources/SecurityAuditLogResource.php)

1. **Encadenamiento Criptográfico (Hash Chaining):**
   * Cada registro incluye la columna `previous_hash`, la cual almacena la firma digital `signature_hmac` del registro inmediatamente anterior, formando una cadena continua idéntica a una estructura de bloques (Blockchain).
2. **Firma Digital HMAC-SHA256:**
   * La firma se calcula concatenando todos los atributos sensibles (ID de usuario, tipo de evento, severidad, IP, contexto JSON, hash previo y marca temporal) y firmándolos con la clave criptográfica del servidor (`config('app.key')`).
3. **Inmutabilidad Absoluta en Eloquent:**
   * En `SecurityAuditLog::booted()` se interceptan los eventos `updating` y `deleting`:
     ```php
     static::updating(fn () => throw new \DomainException('VIOLACIÓN FORENSE: Los registros de auditoría son inmutables.'));
     static::deleting(fn () => throw new \DomainException('VIOLACIÓN FORENSE: Está prohibido eliminar registros de auditoría.'));
     ```
4. **Auditoría de Integridad en Tiempo Real:**
   * El método `$log->verifyIntegrity(): bool` recalcula la firma HMAC contra los datos actuales. Si un intruso con acceso directo a la base de datos (ej. phpMyAdmin o SQL Injection) modifica una IP o un contexto, el panel muestra de inmediato la alerta crítica en color rojo: **`ADULTERADO`**.

---

### Módulo 5: Restricciones MySQL y Protección contra Escalada de Privilegios
* **Migración:** [`database/migrations/2026_10_25_230003_add_database_level_check_constraints.php`](file:///c:/laragon/www/catalogo-arriendo/database/migrations/2026_10_25_230003_add_database_level_check_constraints.php)
* **Modelo:** [`app/Models/User.php`](file:///c:/laragon/www/catalogo-arriendo/app/Models/User.php) y [`app/Models/SiteSetting.php`](file:///c:/laragon/www/catalogo-arriendo/app/Models/SiteSetting.php)

1. **Restricciones `CHECK` en Motor MySQL:**
   * Se aplican reglas formales a nivel de tabla para impedir precios o stock negativos, incluso si un fallo de software intentara persistirlos:
     * `ALTER TABLE products ADD CONSTRAINT chk_products_price_non_negative CHECK (rental_price >= 0);`
     * `ALTER TABLE products ADD CONSTRAINT chk_products_stock_non_negative CHECK (stock >= 0);`
2. **Defensa contra Asignación Masiva (Mass Assignment):**
   * Se retiraron de `$fillable` los campos sensibles: `is_admin`, `is_developer`, `two_factor_secret`, `two_factor_recovery_codes`, `last_totp_slice` y `mfa_locked_until`.
3. **Anti-SSRF y Anti-XSS en `SiteSetting`:**
   * Se valida con expresión regular anclada (`\A...\z`) que la URL de mapas de Google u OpenStreetMap no contenga saltos de línea (`%0A` o `\n`) ni protocolos maliciosos como `javascript:alert(1)`.

---

### Módulo 6: Endurecimiento de Cabeceras HTTP
* **Middleware:** [`app/Http/Middleware/SecurityHeadersMiddleware.php`](file:///c:/laragon/www/catalogo-arriendo/app/Http/Middleware/SecurityHeadersMiddleware.php)

Se inyectan las siguientes cabeceras en cada respuesta HTTP:
* `X-Frame-Options: DENY` en `/admin/*` y `SAMEORIGIN` en el catálogo público (Anti-Clickjacking).
* `X-Content-Type-Options: nosniff` (Anti-MIME Confusion).
* `X-Permitted-Cross-Domain-Policies: none` (Aislamiento de Flash/PDF).
* `Cross-Origin-Opener-Policy: same-origin` (Aislamiento de contexto de ventana contra Specter/Meltdown en navegadores).
* `Content-Security-Policy: object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'self';` (Restricción estricta de ejecución de scripts y embebidos).

---

### Módulo 7: Suite de Pruebas Automatizadas de Intrusión (Red Team)
* **Archivo de Pruebas:** [`tests/Feature/Security/RedTeamSecurityTest.php`](file:///c:/laragon/www/catalogo-arriendo/tests/Feature/Security/RedTeamSecurityTest.php)

Contiene 9 pruebas de penetración automatizadas ejecutadas con PHPUnit:
1. `test_totp_replay_attack_is_blocked_within_same_30_second_window`: Comprueba el bloqueo inmediato al reutilizar el mismo código dentro del mismo intervalo de 30 segundos.
2. `test_recovery_code_can_only_be_used_once_and_resists_reuse`: Verifica que los códigos de emergencia sean estrictamente de un solo uso.
3. `test_mfa_brute_force_triggers_15_minute_account_lockout_and_audit_log`: Simula ataque de fuerza bruta con 5 intentos fallidos y valida el bloqueo por 15 minutos (Circuit Breaker).
4. `test_polyglot_image_with_embedded_php_payload_is_rejected_or_stripped`: Simula una imagen manipulada con código PHP inyectado y verifica su rechazo o sanitización total.
5. `test_security_audit_log_is_immutable_and_detects_database_tampering`: Intenta modificar/eliminar registros verificando el disparo de `DomainException`, y simula alteración directa en MySQL verificando que `$log->verifyIntegrity()` retorne `false`.
6. `test_mass_assignment_cannot_escalate_user_to_admin_or_alter_mfa`: Valida que ningún atacante pueda auto-asignarse privilegios mediante peticiones masivas.
7. `test_site_setting_blocks_newline_bypass_and_javascript_uri_in_map_iframe`: Verifica el bloqueo de evasión con saltos de línea e inyecciones de script en el iframe de mapas.
8. `test_unverified_mfa_admin_cannot_access_filament_resources_directly`: Comprueba que un administrador con 2FA activo sin verificar sea redirigido forzosamente al reto de seguridad.
9. `test_developer_sees_auditoria_label_while_others_see_auditoria_forense_and_logs`: Valida el cambio dinámico de etiquetas en la navegación según el usuario autenticado.

---

### Módulo 8: Cuenta de Desarrollo y Responsividad Integral del Panel

1. **Credenciales de Desarrollador:**
   * Usuario: `dev@catalogo-arriendo.cl`
   * Clave: `DevPassword2026!#`
   * Permisos: Acceso total (`is_admin = true`, `is_developer = true`), protegido contra auto-eliminación o suspensión.
   * En la navegación lateral se simplificó el texto a **`Auditoría`** exclusivamente para esta cuenta. Para otros administradores se mantiene el título formal **`Auditoría Forense & Logs`**.
2. **Responsividad de Pantallas:**
   * Se aplicaron reglas elásticas (`columns(['default' => 1, 'md' => 2])`) en los 8 recursos y widgets del panel administrativo.
   * Se incorporó scroll táctil horizontal en tablas y se ocultaron columnas accesorias mediante `toggleable(isToggledHiddenByDefault: true)` en pantallas de smartphone.

---

## 3. Glosario de Ciberseguridad y Términos Técnicos

* **RFC 6238 (TOTP - Time-Based One-Time Password):**  
  Estándar de la Internet Engineering Task Force que describe el algoritmo matemático para generar códigos de autenticación de un solo uso basados en una clave secreta compartida y el tiempo actual sincronizado en ventanas de 30 segundos.

* **Replay Attack (Ataque de Reutilización):**  
  Técnica donde un atacante intercepta una transmisión válida de datos (como un código 2FA o un token) y la retransmite más tarde para obtener acceso no autorizado. Mitigado registrando el `last_totp_slice`.

* **Race Condition (Condición de Carrera):**  
  Falla que ocurre cuando múltiples procesos o peticiones concurrentes intentan acceder y modificar un recurso compartido al mismo tiempo. En autenticación, un atacante envía el mismo código decenas de veces en el mismo milisegundo buscando que varias peticiones se aprueben antes de que el sistema registre el código como consumido.

* **Pessimistic Locking (`lockForUpdate`):**  
  Mecanismo de base de datos que bloquea la fila consultada a nivel de motor (`SELECT ... FOR UPDATE`), impidiendo que otras transacciones concurrentes lean o modifiquen el registro hasta que la transacción activa se confirme (`COMMIT`) o cancele (`ROLLBACK`).

* **Circuit Breaker (Disyuntor de Seguridad):**  
  Patrón de diseño defensivo que interrumpe temporalmente las operaciones sobre una cuenta tras detectar anomalías o intentos fallidos repetidos (ej. 5 códigos incorrectos), bloqueando el acceso durante 15 minutos para frenar ataques de fuerza bruta.

* **Modo Sudo:**  
  Mecanismo de re-autenticación que exige al usuario ingresar nuevamente su contraseña antes de permitirle realizar acciones altamente críticas (activar/desactivar 2FA o alterar permisos), previniendo ataques si una sesión queda desatendida físicamente.

* **Livewire Snapshot Tampering:**  
  Vector de ataque donde un atacante altera el objeto JSON criptográfico que Livewire intercambia entre cliente y servidor (`wire:snapshot`) para alterar propiedades internas o escalar privilegios. Se neutraliza mediante el atributo `#[Locked]`.

* **Archivo Políglota (Polyglot File):**  
  Archivo especialmente diseñado que es válido simultáneamente en dos formatos diferentes; por ejemplo, un archivo que los navegadores interpretan como una imagen JPEG legítima pero que al mismo tiempo contiene código PHP interpretable por el servidor si se ejecuta.

* **Re-codificación Destructiva (Pixel Re-encoding):**  
  Proceso de seguridad que toma un archivo de imagen subido, descarta completamente su estructura de archivo, lee únicamente el mapa de píxeles sin procesar y lo re-dibuja en un nuevo lienzo en memoria para guardarlo desde cero, eliminando cualquier código oculto.

* **Metadatos EXIF (Exchangeable Image File Format):**  
  Datos adicionales que las cámaras y teléfonos adjuntan a las fotografías (coordenadas GPS, modelo de cámara, fecha, etc.). Representan un riesgo de fuga de privacidad y un vector de inyección de código si un atacante incrusta PHP en ellos.

* **EOF Injection (End of File Injection):**  
  Técnica que añade código malicioso al final de un archivo válido más allá del marcador de fin de archivo (por ejemplo, después del byte `FF D9` en un JPEG).

* **HMAC-SHA256 (Hash-based Message Authentication Code):**  
  Código de autenticación que utiliza una función criptográfica hash (SHA-256) combinada con una clave secreta privada. Garantiza tanto la integridad de los datos como la autenticidad del emisor.

* **Hash Chaining (Encadenamiento Criptográfico):**  
  Técnica criptográfica donde cada registro o bloque almacena el hash del registro previo. Cualquier intento de modificar, intercalar o borrar un registro intermedio altera toda la cadena posterior, delatando la manipulación de forma inmediata.

* **Inmutabilidad Forense:**  
  Propiedad que asegura que un registro, una vez escrito, no pueda ser modificado ni eliminado bajo ninguna circunstancia a nivel de aplicación (disparando excepciones) para preservar evidencia legal o forense.

* **Restricción `CHECK` (MySQL):**  
  Regla declarativa a nivel de motor relacional que evalúa una condición booleana en cada `INSERT` o `UPDATE`. Si el valor viola la condición (ej. precio negativo), el motor de base de datos rechaza la operación automáticamente.

* **Mass Assignment (Asignación Masiva):**  
  Vulnerabilidad en frameworks web donde los datos enviados por el usuario en una petición HTTP se pasan directamente a un modelo Eloquent sin filtrar, permitiendo a un atacante sobreescribir columnas protegidas (como `is_admin` o `password`).

* **SSRF (Server-Side Request Forgery):**  
  Ataque donde el atacante induce al servidor web a realizar peticiones HTTP hacia destinos no autorizados, frecuentemente redes internas o interfaces de metadatos de la nube.

* **CSP (Content Security Policy):**  
  Cabecera de respuesta HTTP que permite a los administradores del sitio restringir los recursos (JavaScript, CSS, imágenes, iframes) que el navegador tiene permitido cargar para esa página, mitigando ataques de Cross-Site Scripting (XSS).

* **Session Fixation (Fijación de Sesión):**  
  Ataque donde el atacante establece el ID de sesión de un usuario legítimo antes de que este inicie sesión. Una vez autenticado el usuario, el atacante utiliza el ID conocido para suplantarlo. Se previene llamando a `session()->regenerate()`.

* **Zero-Trust (Confianza Cero):**  
  Filosofía de seguridad informática que asume que las amenazas existen tanto dentro como fuera de la red corporativa, exigiendo verificación estricta y continua para cada usuario, dispositivo y petición.
