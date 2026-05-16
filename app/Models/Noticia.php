<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    /** @use HasFactory<\Database\Factories\NoticiaFactory> */
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'imagenes',
        'link',
        'creado_por_id',
    ];

    protected function casts(): array
    {
        return [
            'imagenes' => 'array',
        ];
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por_id');
    }
}
