# TODO - CRUD TipoTarea y TipoCierre

## Step 1 - Modelo/relaciones
- [ ] Actualizar `app/Models/Task.php` para incluir relaciones:
  - `tipoTarea()` => belongsTo TipoTarea
  - `tipoCierre()` => belongsTo TipoCierre
- [ ] Actualizar `app/Models/TipoTarea.php` y `app/Models/TipoCierre.php` para relaciones inversas (hasMany tasks).

## Step 2 - Migraciones DB (relacional)
- [ ] Crear migración para agregar columnas `tasks.tipo_tarea_id` y `tasks.tipo_cierre_id` (nullable) y su foreign key.
- [ ] Backfill desde columnas existentes:
  - `tasks.tipo_tarea` (string) -> `tipo_tarea_id`
  - `tasks.tipo_cierre` (si existiera como string) o lógica actual (ver EditTask) -> `tipo_cierre_id`.
- [ ] Eliminar columnas string/ajustar fillable si corresponde.

## Step 3 - Filament CRUD (manual)
- [ ] Crear `app/Filament/Resources/TipoTareaResource.php`.
- [ ] Crear `app/Filament/Resources/TipoCierreResource.php`.
- [ ] Crear forms/tables con columnas básicas (nombre/descripcion si existieran; si no, al menos id/timestamps).

## Step 4 - Integrar en Filament Tasks
- [ ] Actualizar `app/Filament/Resources/Tasks/Schemas/TaskForm.php`:
  - Reemplazar `Select::make('tipo_tarea')` hardcodeado por `Select::make('tipo_tarea_id')` con `->relationship('tipoTarea','nombre')`.
- [ ] Actualizar `app/Filament/Resources/Tasks/Tables/TasksTable.php`:
  - Mostrar `tipoTarea.nombre` en lugar de `tipo_tarea`.
  - Ajustar filtro `tipo_tarea`.
- [ ] Actualizar `app/Filament/Resources/Tasks/Pages/EditTask.php`:
  - En el modal `finalize`, reemplazar `Select::make('tipo_cierre')` por `tipo_cierre_id` relacionado.
  - Ajustar `finalize()` para guardar el id.

## Step 5 - Test rápido
- [ ] Ejecutar migraciones.
- [ ] Validar que el formulario y el modal de finalización funcionen.
- [ ] Validar listados/exports si aplica.

