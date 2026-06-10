<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'detalle',
        'estado',
        'fecha_creacion',
        'ultima_modificacion',
        'usuario_solicita_id',
        'asignado_a_id',
        'area_id',
        'fecha_finalizacion',

        // Estas columnas existen en migración 2026_06_04_000000_add_tipo_uso_tarea_prioridad_to_tasks_table.php.
        // Si el esquema aún no está aplicado en el entorno destino, Laravel fallará al hacer insert/update.
        'tipo_uso',
        'tipo_tarea',
        'prioridad',


        // Solo para uso externo
        'cliente_id',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'ultima_modificacion' => 'datetime',
        'fecha_finalizacion' => 'datetime',
    ];

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'usuario_solicita_id');
    }

    public function asignadoA()
    {
        return $this->belongsTo(User::class, 'asignado_a_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function histories()
    {
        return $this->hasMany(TaskHistory::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
