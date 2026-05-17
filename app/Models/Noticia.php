<?php

namespace App\Models;

use Database\Factories\NoticiaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Noticia extends Model
{
    /** @use HasFactory<NoticiaFactory> */
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
