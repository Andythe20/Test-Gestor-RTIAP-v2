# 🌡️ Monitor de Termometría IoT (Multi-tenant)

Este proyecto es un prototipo de plataforma SaaS para el monitoreo de sensores térmicos industriales. Implementa una arquitectura **Multi-tenancy** (Multi-inquilino) donde cada cliente posee su propia base de datos aislada para garantizar la seguridad y escalabilidad de los datos.

## 🏗️ Arquitectura del Sistema

El sistema utiliza **Spatie Laravel Multitenancy** y divide la lógica en dos niveles:

1. **Landlord (Central):** Gestiona la lista de clientes (tenants) y sus dominios.
2. **Tenant (Cliente):** Base de datos independiente que contiene:
    - **Sucursales:** Ubicaciones físicas del cliente.
    - **Sistemas:** Túneles de frío, hornos, cámaras, etc.
    - **Sensores:** Dispositivos IoT con lecturas térmicas.

## ✨ Características

-   Arquitectura multi-tenant con bases de datos aisladas.
-   Panel de administración para super-administradores.
-   Monitoreo en tiempo real de sensores térmicos.
-   Interfaz moderna con Vue 3 y TailwindCSS.
-   Integración con Inertia.js para una experiencia SPA.

## 🛠️ Stack Tecnológico

-   **Backend:** Laravel 10/11
-   **Multitenancy:** Spatie Laravel Multitenancy
-   **Frontend:** Vue 3 (Composition API) + Inertia.js
-   **Estilos:** TailwindCSS
-   **Iconos:** FontAwesome
-   **Base de Datos:** MySQL
-   **Build Tool:** Vite

## 📋 Prerrequisitos

-   PHP >= 8.1
-   Composer
-   Node.js & NPM
-   Servidor MySQL activo

## 🚀 Instalación y Configuración

Sigue estos pasos estrictamente para levantar el entorno de desarrollo.

### 1. Clonación del Repositorio

```bash
git clone <url-de-tu-repo>
cd nombre-del-proyecto
```

### 2. Instalación de Dependencias

```bash
# Dependencias PHP
composer install

# Dependencias JavaScript
npm install
```

### 3. Configuración del Entorno

```bash
# Copiar archivo de entorno
cp .env.example .env

# Generar llave de aplicación
php artisan key:generate
```

### 4. Configuración de Base de Datos

Abre el archivo `.env` y configura las siguientes variables. Es **CRUCIAL** cambiar la `APP_URL` para que coincida con los dominios del sistema.

**Nota:** Asegúrate de crear manualmente la base de datos `termometria_central_db` en tu gestor MySQL antes de continuar. El usuario de base de datos debe tener permisos para **CREAR** nuevas bases de datos (`CREATE DATABASE` privileges), ya que el sistema las generará automáticamente.

Ejemplo de configuración en `.env`:

```env
APP_NAME="Monitor de Termometría"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://termometria.test:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=termometria_central_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

# Configuración para multitenancy
LANDLORD_DB_DATABASE=termometria_central_db
```

### 5. Configuración de Dominios Locales (Hosts)

Para que el sistema multi-tenant detecte los subdominios (ej: `walmart.termometria.test`) en local, debes editar tu archivo de hosts.

**Ruta del archivo:**

-   **Windows:** `C:\Windows\System32\drivers\etc\hosts` (Editar como Administrador)
-   **Linux/Mac:** `/etc/hosts` (Usar `sudo nano /etc/hosts`)

Agrega estas líneas al final del archivo:

```
127.0.0.1 termometria.test
127.0.0.1 walmart.termometria.test
127.0.0.1 costco.termometria.test
# Agrega más según los tenants que tengas
```

### 6. Migraciones y Seeders

Este proyecto usa una estrategia de migración separada. **No uses** `php artisan migrate` a secas.

#### Migrar el Landlord (Base de Datos Central)

Esto crea las tablas de tenants, users, sessions, etc. en la base central.

```bash
php artisan migrate --path=database/migrations/landlord --database=landlord
```

#### Ejecutar el Seeder Maestro

Este comando crea los clientes, genera físicamente sus bases de datos, migra y puebla con datos de prueba (Empresas, Sucursales, Sistemas y Sensores).

```bash
php artisan db:seed --class=LandlordSeeder
```

### 7. Ejecutar el Proyecto

Necesitas dos terminales abiertas:

**Terminal 1 (Backend - Laravel):**

```bash
php artisan serve --host=termometria.test
```

**Terminal 2 (Frontend - Vite):**

```bash
npm run dev
```

### 8. Acceso

Abre tu navegador e ingresa a:

-   **Panel Super-Admin:** http://termometria.test:8000
    -   Visualiza todos los clientes y accede a sus datos.

## 🐛 Solución de Problemas Comunes

-   **Error: "Vite manifest not found"**

    -   Asegúrate de tener `npm run dev` corriendo en otra terminal.

-   **Error: "No database selected"**

    -   Verifica que tu usuario MySQL tenga permisos para crear bases de datos y que el `LandlordSeeder` se haya ejecutado completamente.

-   **Error 500 al entrar**

    -   Asegúrate de haber migrado las tablas de sesión y usuarios al landlord (paso 6.1).

-   **Problemas con subdominios**
    -   Verifica que los hosts estén configurados correctamente y que el servidor esté corriendo en `termometria.test`.

---

¡Gracias por usar el Monitor de Termometría IoT! Si tienes preguntas, abre un issue en el repositorio.
