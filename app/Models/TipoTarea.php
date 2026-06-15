<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class TipoTarea extends Model
{
    protected $table = 'tipo_tareas';

    protected $fillable = [
        'nombre',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'tipo_tarea_id');
    }
}

