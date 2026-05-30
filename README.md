

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

## Importación de clientes desde Excel/CSV

La aplicación permite importar clientes desde un archivo `.csv` o `.xlsx` vía el recurso `Cliente` en el panel de Filament.

- Formatos aceptados: `.csv`, `.xlsx`.
- Las cabeceras se normalizan (insensible a mayúsculas/minúsculas y espacios).
- Campos obligatorios (por fila):
	- `numero_cuenta` — identificador único de la cuenta (se usa para `updateOrCreate`).
	- `nombre_cuenta` — nombre asociado a la cuenta.
- Campos opcionales para importar CBU en la misma fila:
	- `banco` — nombre del banco.
	- `cbu` — número de CBU.
	- `observaciones` — texto libre con notas.

Reglas de importación:
- Solo se procesan las filas que contienen ambos campos obligatorios; las filas con cualquiera de estos vacíos se saltan.
- Si `numero_cuenta` ya existe en la base de datos, se actualiza `nombre_cuenta`.
- Si el archivo contiene las columnas opcionales `banco`, `cbu` y/o `observaciones`, la importación intentará crear el registro de CBU asociado al cliente (una fila = un CBU). Si estos campos están vacíos, no se creará CBU para esa fila.

Ejemplo de cabecera válida (CSV o Excel):

numero_cuenta,nombre_cuenta,banco,cbu,observaciones

Pasos para usar la importación desde la UI:
1. Ir al panel de Filament → `Clientes`.
2. Hacer clic en `Importar clientes` (botón en la barra de herramientas).
3. Seleccionar el archivo `.csv` o `.xlsx` con la cabecera adecuada.
4. Confirmar; las filas válidas se importarán/actualizarán.

Comandos útiles para preparar el entorno si hace falta:
```bash
composer dump-autoload
php artisan migrate
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

