<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteCbu extends Model
{
    protected $fillable = [
        'cliente_id',
        'banco',
        'cbu',
        'observaciones',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
