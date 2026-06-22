<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';

    protected $fillable = [
        'tipo',
        'titulo',
        'descripcion',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'duracion_minutos',
        'resultado',
        'cliente_id',
        'oportunidad_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'duracion_minutos' => 'integer',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function oportunidad(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class, 'oportunidad_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'llamada' => 'Llamada',
            'reunion' => 'Reunion',
            'email' => 'Email',
            default => $this->tipo,
        };
    }

    public function getTipoColorAttribute(): string
    {
        return match ($this->tipo) {
            'llamada' => 'info',
            'reunion' => 'warning',
            'email' => 'success',
            default => 'gray',
        };
    }
}
