<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'cliente_id',
        'user_id',
        'monto_estimado',
        'probabilidad',
        'fecha_esperada_cierre',
        'etapa',
        'origen',
        'fuente',
        'metadata',
        'ganada_at',
        'perdida_at',
        'motivo_perdida',
    ];

    protected $casts = [
        'monto_estimado' => 'decimal:2',
        'probabilidad' => 'integer',
        'fecha_esperada_cierre' => 'date',
        'metadata' => 'array',
        'ganada_at' => 'datetime',
        'perdida_at' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'oportunidad_id');
    }

    public function etapa(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => strtolower($value),
        );
    }

    public function scopeEtapa($query, string $etapa)
    {
        return $query->where('etapa', $etapa);
    }

    public function scopeAbiertas($query)
    {
        return $query->whereNotIn('etapa', ['ganada', 'perdida']);
    }

    public function scopeCerradas($query)
    {
        return $query->whereIn('etapa', ['ganada', 'perdida']);
    }

    public function scopeGanadas($query)
    {
        return $query->where('etapa', 'ganada');
    }

    public function scopePerdidas($query)
    {
        return $query->where('etapa', 'perdida');
    }

    public function getEtapasOptions(): array
    {
        return [
            'prospeccion' => 'Prospección',
            'calificacion' => 'Calificación',
            'propuesta' => 'Propuesta',
            'negociacion' => 'Negociación',
            'ganada' => 'Ganada',
            'perdida' => 'Perdida',
        ];
    }

    public function getEtapaLabel(): string
    {
        return $this->getEtapasOptions()[$this->etapa] ?? $this->etapa;
    }

    public function getProbabilidadPorEtapa(): int
    {
        return match ($this->etapa) {
            'prospeccion' => 10,
            'calificacion' => 25,
            'propuesta' => 50,
            'negociacion' => 75,
            'ganada' => 100,
            'perdida' => 0,
            default => 10,
        };
    }

    public function marcarComoGanada(): void
    {
        $this->update([
            'etapa' => 'ganada',
            'probabilidad' => 100,
            'ganada_at' => now(),
        ]);
    }

    public function marcarComoPerdida(string $motivo = ''): void
    {
        $this->update([
            'etapa' => 'perdida',
            'probabilidad' => 0,
            'perdida_at' => now(),
            'motivo_perdida' => $motivo,
        ]);
    }

    public function avanzarEtapa(): void
    {
        $etapas = array_keys($this->getEtapasOptions());
        $currentIndex = array_search($this->etapa, $etapas);

        if ($currentIndex !== false && $currentIndex < count($etapas) - 1) {
            $nuevaEtapa = $etapas[$currentIndex + 1];
            $this->update([
                'etapa' => $nuevaEtapa,
                'probabilidad' => $this->getProbabilidadPorEtapa(),
            ]);
        }
    }

    public function retrocederEtapa(): void
    {
        $etapas = array_keys($this->getEtapasOptions());
        $currentIndex = array_search($this->etapa, $etapas);

        if ($currentIndex !== false && $currentIndex > 0) {
            $nuevaEtapa = $etapas[$currentIndex - 1];
            $this->update([
                'etapa' => $nuevaEtapa,
                'probabilidad' => $this->getProbabilidadPorEtapa(),
            ]);
        }
    }
}
