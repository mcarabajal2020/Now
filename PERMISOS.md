# Sistema de Permisos y Autorización

## Descripción General

El sistema de permisos permite controlar qué usuarios pueden **ver**, **editar**, o **no ver** (oculto) los recursos de la aplicación (Tasks, Noticias, Users).

## Arquitectura

### Modelos

1. **Role**: Define roles base (Admin, Editor, Viewer)
2. **Permission**: Define permisos sobre recursos (tasks, noticias, users) con acciones (ver, editar, eliminar)
3. **User**: Asociado a un rol base
4. **UserPermission**: Excepciones de permisos individuales por usuario

### Tablas

- `roles`: Roles con descripción
- `permissions`: Permisos sobre recursos y acciones
- `role_permissions`: Relación muchos a muchos entre roles y permisos
- `user_permissions`: Excepciones de permisos individuales
- `users`: Campo `role_id` agregado

## Roles Predefinidos

### Admin
- Permiso total: Ver, Editar, Eliminar en Tasks, Noticias, Users

### Editor
- Ver y Editar en Tasks, Noticias, Users (sin eliminar)

### Viewer
- Solo Ver en Tasks, Noticias, Users

## Flujo de Permisos

Cuando se verifica si un usuario puede acceder a un recurso:

1. **Primero**: Se revisa si hay una excepción de usuario (`UserPermission`)
   - Si es `oculto`: No puede ver
   - Si es `ver` o `editar`: Usa ese permiso
2. **Segundo**: Si no hay excepción, se revisa el rol del usuario
3. **Tercero**: Si no tiene rol, no tiene permisos

## Métodos del Modelo User

### `canViewResource($recurso): bool`
Verifica si el usuario puede ver un recurso
```php
$user->canViewResource('tasks'); // true/false
```

### `canEditResource($recurso): bool`
Verifica si el usuario puede editar un recurso
```php
$user->canEditResource('tasks'); // true/false
```

### `getPermission($recurso): ?string`
Obtiene el nivel de permiso: 'ver', 'editar', o null
```php
$permission = $user->getPermission('tasks'); // 'ver', 'editar', o null
```

## Políticas de Autorización

Las políticas (`TaskPolicy`, `NoticiaPolicy`, `UserPolicy`) usan los métodos del usuario para autorizar acciones:

```php
// En TaskPolicy
public function viewAny(User $user): bool
{
    return $user->canViewResource('tasks');
}

public function update(User $user, Task $task): bool
{
    return $user->canEditResource('tasks');
}
```

## Administración en Filament

### Gestionar Roles
- Navega a **Roles** en Filament
- Crea nuevos roles
- Asigna permisos a roles (relación muchos a muchos)

### Gestionar Permisos
- Navega a **Permisos** en Filament
- Visualiza todos los permisos disponibles
- Los permisos se crean automáticamente al seeder

### Gestionar Excepciones de Permisos
- Navega a **Excepciones de Permisos** en Filament
- Crea excepciones para usuarios específicos
- Elige entre:
  - **Ver**: El usuario puede ver el recurso
  - **Editar**: El usuario puede ver y editar el recurso
  - **Oculto**: El usuario NO puede ver el recurso (tiene mayor prioridad que el rol)

## Usuarios de Prueba

### Credenciales

| Email | Contraseña | Rol |
|-------|-----------|-----|
| admin@test.com | password | Admin |
| editor@test.com | password | Editor |
| viewer@test.com | password | Viewer |

## Uso en Código

### Verificar permisos en controladores
```php
use Gate;

if (Gate::denies('viewAny', Task::class)) {
    abort(403);
}

// O usando middleware
Route::get('/tasks', TaskController::class)->middleware('can:viewAny,App\Models\Task');
```

### Usar el trait en recursos Filament
```php
use App\Filament\Traits\AuthorizedResource;

class TaskResource extends Resource
{
    use AuthorizedResource;
    // ...
}
```

El trait automáticamente:
- Oculta el recurso del menú si el usuario no tiene permiso
- Previene acceso a crear si no tiene permiso de editar
- Verifica permisos en acciones

### Renderizar componentes condicionalmente en Blade
```blade
@can('viewAny', App\Models\Task::class)
    <a href="/tasks">Ver Tasks</a>
@endcan

@can('update', $task)
    <button>Editar</button>
@endcan
```

## Crear un nuevo recurso con permisos

1. Crear el modelo y migraciones
2. Crear la política
3. Crear el recurso Filament
4. Agregar el trait `AuthorizedResource`
5. Crear permisos en `PermissionSeeder`

## Excepciones y Casos Especiales

### Ocultar recurso para un usuario específico
```php
// Crear una excepción UserPermission
UserPermission::create([
    'user_id' => $userId,
    'recurso' => 'tasks',
    'accion' => 'oculto',
]);
```

### Cambiar permisos de un rol
```php
$role = Role::where('nombre', 'editor')->first();
$role->permissions()->sync($newPermissionIds);
```

### Cambiar rol de un usuario
```php
$user = User::find($userId);
$user->update(['role_id' => Role::where('nombre', 'viewer')->first()->id]);
```

## Notas Importantes

- Los permisos se verifica en tiempo real (no hay caché)
- Si un usuario no tiene rol asignado, no tiene permisos
- Las excepciones de usuario tienen prioridad sobre los permisos del rol
- El permiso `oculto` es la forma más restrictiva
- Los administradores con el rol `admin` tienen acceso a todo

## Tabla de Decisión

| Rol | Excepción | Resultado |
|-----|-----------|-----------|
| viewer | - | Ver solo |
| editor | - | Ver + Editar |
| admin | - | Ver + Editar + Eliminar |
| cualquiera | oculto | No visible |
| cualquiera | ver | Ver solo (override rol) |
| cualquiera | editar | Ver + Editar (override rol) |

