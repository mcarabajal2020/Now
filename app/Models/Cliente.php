<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'numero_cuenta',
        'nombre_cuenta',
    ];

    public function cbus()
    {
        return $this->hasMany(ClienteCbu::class);
    }
}
