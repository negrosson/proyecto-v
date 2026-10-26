# Credenciales de Acceso al Panel Administrativo
**Proyecto:** Catálogo & Sistema de Arriendos para Eventos  
**Fecha:** Octubre 2026  
**Entorno:** Desarrollo / Staging / Producción  

---

## 1. URLs de Ingreso al Panel

* **Acceso Local (Laragon):** [http://catalogo-arriendo.test/admin](http://catalogo-arriendo.test/admin)
* **Acceso Remoto (Túnel ngrok):**  
  Para generar un enlace público HTTPS accesible desde cualquier dispositivo o teléfono:
  ```bash
  ngrok http --host-header=catalogo-arriendo.test 80
  ```
  *(Ingresar a la URL HTTPS generada terminada en `/admin`).*

---

## 2. Cuentas y Credenciales de Acceso

### Cuenta 1: Desarrollador (Acceso Técnico & Control de Auditoría)
Cuenta creada exclusivamente para ti como desarrollador del sistema. Cuenta con permisos totales y visualización técnica limpia.

* **Nombre de Usuario:** `Desarrollador`
* **Correo Electrónico:** `dev@catalogo-arriendo.cl`
* **Contraseña:** `DevPassword2026!#`
* **Rol en el Sistema:** Desarrollador (`is_developer = true`, `is_admin = true`)
* **Comportamiento en el Menú Lateral:**
  * El módulo de registros se muestra de forma simplificada como: **`Auditoría`**.
* **Protecciones:** Cuenta blindada contra suspensión o eliminación involuntaria.

---

### Cuenta 2: Administrador General (Dueño del Negocio / Operaciones)
Cuenta corporativa principal asignada a la administración de Arriendos & Eventos.

* **Nombre de Usuario:** `Administrador General`
* **Correo Electrónico:** `admin@arriendoseventos.cl`
* **Contraseña:** `Admin2026!#Secure`
* **Rol en el Sistema:** Super Administrador (`is_admin = true`, `is_developer = false`)
* **Comportamiento en el Menú Lateral:**
  * El módulo de registros se muestra con el nombre institucional completo: **`Auditoría Forense & Logs`**.
* **Protecciones:** Cuenta principal protegida contra auto-eliminación.

---

### Cuenta 3: Administrador de Respaldo / Auxiliar
Cuenta alternativa para pruebas de contingencia o soporte operativo.

* **Nombre de Usuario:** `admin`
* **Correo Electrónico:** `admin@admin.com`
* **Contraseña:** `Admin2026!#Secure`
* **Rol en el Sistema:** Super Administrador (`is_admin = true`, `is_developer = false`)
* **Comportamiento en el Menú Lateral:**
  * Muestra **`Auditoría Forense & Logs`**.

---

## 3. Resumen Comparativo de Roles

| Correo Electrónico | Rol Asignado | Etiqueta en Menú | Permisos Administrativos | Blindaje Anti-Borrado |
| :--- | :--- | :--- | :--- | :--- |
| `dev@catalogo-arriendo.cl` | **Desarrollador** | **`Auditoría`** | Acceso Total (100%) | **Activo** |
| `admin@arriendoseventos.cl` | **Super Administrador** | **`Auditoría Forense & Logs`** | Acceso Total (100%) | **Activo** |
| `admin@admin.com` | **Super Administrador** | **`Auditoría Forense & Logs`** | Acceso Total (100%) | Estándar |

---

## 4. Notas de Seguridad Operativa

1. **Doble Factor de Autenticación (2FA - TOTP):**
   * Una vez iniciada la sesión, puedes dirigirte a **Seguridad & Accesos > Doble Factor (2FA)** para vincular tu app de autenticación (Google Authenticator, Microsoft Authenticator o 1Password).
   * No requiere ninguna cuenta de Google Cloud ni claves de API: el protocolo RFC 6238 es 100% matemático y autónomo.
2. **Modo Sudo:**
   * Cualquier cambio sensible en seguridad (como activar o desactivar 2FA) solicitará confirmar tu contraseña actual.
3. **Restablecimiento de Contraseñas:**
   * Desde la sección **Seguridad & Accesos > Usuarios y Administradores**, cualquier Super Administrador o Desarrollador puede cambiar la clave de acceso de un usuario utilizando el botón con icono de llave (*"Contraseña"*).
