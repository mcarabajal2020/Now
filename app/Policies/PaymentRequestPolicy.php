<?php

namespace App\Policies;

use App\Models\PaymentRequest;
use App\Models\User;

class PaymentRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PaymentRequest $paymentRequest): bool
    {
        return $this->canManagePaymentRequests($user)
            || $paymentRequest->solicitante_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PaymentRequest $paymentRequest): bool
    {
        if ($paymentRequest->estado === 'terminado') {
            return false;
        }

        return $this->canManagePaymentRequests($user);
    }

    public function delete(User $user, PaymentRequest $paymentRequest): bool
    {
        return false;
    }

    private function canManagePaymentRequests(User $user): bool
    {
        if ($user->role?->nombre === 'admin') {
            return true;
        }

        return (bool) (
            $user->puede_autorizar
            || $user->puede_realizar_pago
            || $user->puede_realizar_transferencia
        );
    }
}
