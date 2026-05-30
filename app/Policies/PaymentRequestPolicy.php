<?php

namespace App\Policies;

use App\Models\PaymentRequest;
use App\Models\User;

class PaymentRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PaymentRequest $paymentRequest): bool
    {
        return $this->canManagePaymentRequests($user) || $paymentRequest->solicitante_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PaymentRequest $paymentRequest): bool
    {
        if ($paymentRequest->estado === 'terminado') {
            return false;
        }

        return $this->canManagePaymentRequests($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaymentRequest $paymentRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PaymentRequest $paymentRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PaymentRequest $paymentRequest): bool
    {
        return false;
    }

    private function canManagePaymentRequests(User $user): bool
    {
        if ($user->role?->nombre === 'admin') {
            return true;
        }

        return (bool) (
            $user->puede_autorizar ||
            $user->puede_realizar_pago ||
            $user->puede_realizar_transferencia
        );
    }
}
