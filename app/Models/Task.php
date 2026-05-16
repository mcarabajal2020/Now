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
        'fecha_finalizacion',
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
}
