# 🌡️ Monitor de Termometría IoT (Multi-tenant)

Este proyecto es un prototipo de plataforma SaaS para el monitoreo de sensores térmicos industriales. Implementa una arquitectura **Multi-tenancy** (Multi-inquilino) donde cada cliente posee su propia base de datos aislada para garantizar la seguridad y escalabilidad de los datos.

## 🏗️ Arquitectura del Sistema

El sistema utiliza **Spatie Laravel Multitenancy** y divide la lógica en dos niveles:

1.  **Landlord (Central):** Gestiona la lista de clientes (tenants) y sus dominios.
2.  **Tenant (Cliente):** Base de datos independiente que contiene:
    -   **Sucursales:** Ubicaciones físicas del cliente.
    -   **Sistemas:** Túneles de frío, hornos, cámaras, etc.
    -   **Sensores:** Dispositivos IoT con lecturas térmicas.

## 🛠️ Stack Tecnológico

-   **Backend:** Laravel 10/11
-   **Multitenancy:** Spatie Laravel Multitenancy
-   **Frontend:** Vue 3 (Composition API) + Inertia.js
-   **Estilos:** TailwindCSS
-   **Iconos:** FontAwesome
-   **Base de Datos:** MySQL

---

## 🚀 Guía de Instalación Local

Sigue estos pasos estrictamente para levantar el entorno de desarrollo.

### 1. Prerrequisitos

-   PHP >= 8.1
-   Composer
-   Node.js & NPM
-   Servidor MySQL activo

### 2. Configuración Inicial

```bash
# 1. Clonar repositorio
git clone <url-de-tu-repo>
cd nombre-del-proyecto

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
npm install

# 4. Copiar archivo de entorno
cp .env.example .env

# 5. Generar llave de aplicación
php artisan key:generate
```
