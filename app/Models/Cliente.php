<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'numero_cuenta',
        'nombre_cuenta',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function cbus()
    {
        return $this->hasMany(ClienteCbu::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }
}
