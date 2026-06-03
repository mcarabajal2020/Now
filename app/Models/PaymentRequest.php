<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentRequest extends Model
{
    protected $fillable = [
        'cliente_id',
        'cliente_cbu_id',
        'numero_cuenta',
        'nombre_cuenta',
        'monto',
        'fecha_pago',
        'observaciones',
        'estado',
        'solicitante_id',
        'autorizado_por_id',
        'autorizado_at',
        'pagado_por_id',
        'pagado_at',
        'transferido_por_id',
        'transferido_at',
        'cancelado_at',
        'cancelado_por_id',
        'cancelacion_observaciones',
        'total_pagado',
        'observaciones_pago',
        'imagenes',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'imagenes' => 'array',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function clienteCbu(): BelongsTo
    {
        return $this->belongsTo(ClienteCbu::class, 'cliente_cbu_id');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function autorizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizado_por_id');
    }

    public function pagadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pagado_por_id');
    }

    public function transferidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferido_por_id');
    }

    public function canceladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por_id');
    }


    public function logs(): HasMany
    {
        return $this->hasMany(PaymentRequestLog::class, 'payment_request_id');
    }

    protected static function booted()
    {
        static::created(function (self $paymentRequest) {
            // Create initial log entry when a payment request is created
            PaymentRequestLog::create([
                'payment_request_id' => $paymentRequest->id,
                'event' => 'creado',
                'user_id' => $paymentRequest->solicitante_id,
                'message' => null,
                'created_at' => $paymentRequest->created_at ?? now(),
            ]);
        });
    }
}
