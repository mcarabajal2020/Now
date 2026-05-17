

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
git clone https://github.com/mcarabajal2020/Now
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
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
