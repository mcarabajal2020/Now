# CRM - Tareas, Clientes y Oportunidades

Sistema de gestión comercial y de tareas desarrollado con **Laravel 13** y **Filament v5**. Interfaz completamente en español.

## Módulos

- **Dashboard**: Panel de control con KPIs, pipeline de oportunidades, agenda y tareas
- **Tickets**: Gestión de tareas/tickets con estados (Nuevo, En Proceso, Finalizado, Cerrado, Rechazado)
- **Oportunidades**: Funnel de ventas tipo Kanban con etapas (Prospección → Calificación → Propuesta → Negociación → Ganada/Perdida)
- **Actividades/Agenda**: Registro de llamadas, reuniones y emails vinculados a clientes y oportunidades
- **Clientes**: Directorio de clientes con gestión de CBU y alias de facturación
- **Pedidos de fondos**: Solicitudes de pago con estados y seguimiento
- **Noticias**: Publicación de novedades internas
- **Usuarios**: Gestión de usuarios con permisos granulares por recurso
- **Áreas**: Organización de áreas de trabajo
- **Tipos de tareas** y **Tipos de cierre**: Catálogos de clasificación

## Permisos

Sistema de permisos uniforme cubriendo todas las acciones (ver, editar) sobre cada recurso:

| Recurso | Ver | Editar | Oculto |
|---|---|---|---|
| Tickets | ✓ | ✓ | ✓ |
| Noticias | ✓ | ✓ | ✓ |
| Oportunidades | ✓ | ✓ | ✓ |
| Clientes | ✓ | ✓ | ✓ |
| Pedidos de fondos | ✓ | ✓ | ✓ |
| Usuarios | ✓ | ✓ | ✓ |
| Áreas | ✓ | ✓ | ✓ |
| Tipos de tareas | ✓ | ✓ | ✓ |
| Tipos de cierre | ✓ | ✓ | ✓ |
| Roles | ✓ | ✓ | ✓ |
| Permisos | ✓ | ✓ | ✓ |
| Actividades | ✓ | ✓ | ✓ |
| Dashboard | ✓ | — | — |

Los permisos se asignan directamente desde el formulario de creación/edición de cada usuario (3 columnas de checkboxes: Puede ver, Puede editar, Oculto). Roles y permisos están disponibles pero ocultos del menú de navegación.

## Requisitos

- PHP 8.4
- Composer
- Node.js
- SQLite o MySQL

## Instalación

```bash
# 1. Clonar
git clone https://github.com/mcarabajal2020/Now
cd Tareas

# 2. Dependencias PHP
composer install

# 3. Entorno
cp .env.example .env
php artisan key:generate

# 4. Migraciones y seeders
php artisan migrate
php artisan db:seed

# 5. Dependencias JS
npm install
npm run build

# 6. Servidor
php artisan serve
```

### Seeders

`php artisan db:seed` crea usuarios de prueba:

| Usuario | Email | Password |
|---|---|---|
| Admin | admin@test.com | password |
| Editor | editor@test.com | password |
| Viewer | viewer@test.com | password |

Si los usuarios no se crean, ejecutar manualmente:

```bash
php artisan db:seed --class=PermissionSeeder
```

## Importación de clientes

Se puede importar clientes desde archivos `.csv` o `.xlsx` desde el recurso Clientes en Filament.

**Campos obligatorios por fila:**

- `numero_cuenta` — identificador único (se usa para `updateOrCreate`)
- `nombre_cuenta` — nombre de la cuenta

**Campos opcionales (para crear CBU asociado):**

- `banco` — nombre del banco
- `cbu` — número de CBU
- `tipo_cbu` — tipo (`c/c`, `c/a`, `cbu`, `cvu`)
- `observaciones` — texto libre

**Cabecera válida:**

```
numero_cuenta,nombre_cuenta,banco,cbu,tipo_cbu,observaciones
```

Pasos:
1. Panel Filament → Clientes
2. Clic en "Importar clientes"
3. Seleccionar archivo `.csv` o `.xlsx`
4. Confirmar

## Tecnologías

| Componente | Versión |
|---|---|
| PHP | 8.4 |
| Laravel | 13 |
| Filament | v5 |
| Livewire | v4 |
| Tailwind CSS | v4 |
| Pest (tests) | v4 |
| Laravel Pint | v1 |
| Laravel Boost | v2 |

## Comandos útiles

```bash
php artisan route:list           # Ver rutas registradas
php artisan migrate:fresh --seed  # Reiniciar base de datos
php artisan test --compact       # Ejecutar tests
vendor/bin/pint --dirty --format agent  # Formatear código
```
