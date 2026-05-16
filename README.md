<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Acerca de la Aplicación

**Tareas y Noticias** es una aplicación web desarrollada con Laravel 13 que permite la gestión de tareas y noticias con un sistema completo de roles y permisos.

### Características principales

- **Gestión de Tareas**: Crear, editar, visualizar y eliminar tareas con seguimiento de estado
- **Gestión de Noticias**: Publicar y gestionar noticias en la plataforma
- **Sistema de Roles y Permisos**: Control granular de acceso basado en roles (Admin, Editor, Viewer)
- **Panel de Administración**: Interfaz administrativa con Filament v5
- **Excepciones de Permisos**: Permitir excepciones individuales a permisos por rol
- **Perfil de Usuario**: Cada usuario puede editar su perfil personal
- **Notificaciones**: Sistema de notificaciones en base de datos

## Requisitos del Sistema

- **PHP 8.4** (requerido)
- **Composer** (para gestión de dependencias)
- **Node.js** (para compilar assets con Vite)
- **SQLite** o **MySQL** (recomendado para producción)

## Instalación

### 1. Clonar el repositorio

```bash
git clone <tu-repositorio>
cd Tareas
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Configurar el archivo de entorno

```bash
cp .env.example .env
```

Edita el archivo `.env` y configura tu base de datos:

```env
DB_CONNECTION=sqlite
# O para MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=tareas
# DB_USERNAME=root
# DB_PASSWORD=
```

### 4. Generar clave de aplicación

```bash
php artisan key:generate
```

### 5. Ejecutar migraciones

```bash
php artisan migrate
```

### 6. Ejecutar seeders (opcional pero recomendado)

```bash
php artisan db:seed
```

Esto creará usuarios de prueba:
- **Admin**: admin@test.com / password
- **Editor**: editor@test.com / password
- **Viewer**: viewer@test.com / password

### 7. Instalar dependencias de Node.js

```bash
npm install
```

### 8. Compilar assets

Para desarrollo:
```bash
npm run dev
```

Para producción:
```bash
npm run build
```

### 9. Iniciar el servidor

```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

Panel administrativo: `http://localhost:8000/admin`

## Sistema de Roles y Permisos

### Roles disponibles

- **Admin**: Acceso total a todas las funcionalidades
- **Editor**: Puede ver y editar tareas y noticias, pero no eliminar
- **Viewer**: Solo lectura de tareas y noticias

### Recursos protegidos

- **Tareas**: Acceso según rol asignado
- **Noticias**: Acceso según rol asignado
- **Usuarios**: Solo administradores pueden ver y gestionar usuarios
- **Roles, Permisos y Excepciones**: Solo administradores

### Perfil Personal

Todos los usuarios pueden editar su perfil personal desde la opción "Mi Perfil" en el menú de administración.

## Información adicional

Para más detalles sobre el sistema de permisos, consulta la documentación en [PERMISOS.md](./PERMISOS.md).

## Versiones de dependencias principales

- Laravel Framework: v13
- Filament: v5
- Livewire: v4
- Tailwind CSS: v4
- Pest: v4
- PHP: 8.4

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
