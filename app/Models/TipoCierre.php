<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCierre extends Model
{
    protected $table = 'tipo_cierres';

    protected $fillable = [
        'nombre',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'tipo_cierre_id');
    }
}

