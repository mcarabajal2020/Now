<?php

use App\Filament\Resources\PaymentRequestResource;
use App\Filament\Resources\PaymentRequestResource\Pages\EditPaymentRequest;
use App\Models\PaymentRequest;
use App\Models\PaymentRequestLog;
use App\Models\Role;
use App\Models\User;

test('authorized users can see the authorize action for pending authorization payment requests', function () {
    $user = User::factory()->create([
        'puede_autorizar' => true,
        'puede_realizar_pago' => true,
        'puede_realizar_transferencia' => true,
    ]);

    $paymentRequest = PaymentRequest::create([
        'monto' => 1500,
        'estado' => 'pendiente_autorizacion',
        'solicitante_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(PaymentRequestResource::getUrl('edit', ['record' => $paymentRequest]))
        ->assertOk()
        ->assertSee('Autorizar')
        ->assertDontSee('Pago realizado')
        ->assertDontSee('Transferencia realizada');
});

test('users without authorization permission cannot see the authorize action', function () {
    $role = Role::create([
        'nombre' => 'admin',
        'descripcion' => 'Administrador',
    ]);

    $user = User::factory()->create([
        'role_id' => $role->id,
        'puede_autorizar' => false,
    ]);

    $paymentRequest = PaymentRequest::create([
        'monto' => 1500,
        'estado' => 'pendiente_autorizacion',
        'solicitante_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(PaymentRequestResource::getUrl('edit', ['record' => $paymentRequest]))
        ->assertOk()
        ->assertDontSee('Autorizar');
});

test('only the creator or an admin can update payment request details', function () {
    $adminRole = Role::create([
        'nombre' => 'admin',
        'descripcion' => 'Administrador',
    ]);

    $creator = User::factory()->create();
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $operator = User::factory()->create(['puede_realizar_pago' => true]);

    $paymentRequest = PaymentRequest::create([
        'monto' => 1500,
        'estado' => 'pendiente_autorizacion',
        'solicitante_id' => $creator->id,
    ]);

    $this->actingAs($creator);
    expect(PaymentRequestResource::canUpdateRequestDetails($paymentRequest))->toBeTrue();

    $this->actingAs($admin);
    expect(PaymentRequestResource::canUpdateRequestDetails($paymentRequest))->toBeTrue();

    $this->actingAs($operator);
    expect(PaymentRequestResource::canUpdateRequestDetails($paymentRequest))->toBeFalse();
});

test('payment request detail fields are ignored during save for non creators', function () {
    $creator = User::factory()->create();
    $operator = User::factory()->create(['puede_realizar_pago' => true]);

    $paymentRequest = PaymentRequest::create([
        'monto' => 1500,
        'estado' => 'pendiente_pago',
        'solicitante_id' => $creator->id,
    ]);

    $page = new EditPaymentRequest;
    $page->record = $paymentRequest;

    $this->actingAs($operator);

    $method = new ReflectionMethod($page, 'mutateFormDataBeforeSave');
    $result = $method->invoke($page, [
        'cliente_id' => 2,
        'numero_cuenta' => '999',
        'nombre_cuenta' => 'Cuenta alterada',
        'cliente_cbu_id' => 3,
        'monto' => 999999,
        'fecha_pago' => '2026-05-30',
        'observaciones' => 'Puede editar esto',
    ]);

    expect($result)->toBe([
        'fecha_pago' => '2026-05-30',
        'observaciones' => 'Puede editar esto',
    ]);
});

test('finished payment requests cannot be edited but can be viewed with their logs', function () {
    $adminRole = Role::create([
        'nombre' => 'admin',
        'descripcion' => 'Administrador',
    ]);

    $creator = User::factory()->create();
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $paymentRequest = PaymentRequest::create([
        'monto' => 1500,
        'estado' => 'terminado',
        'solicitante_id' => $creator->id,
    ]);

    PaymentRequestLog::create([
        'payment_request_id' => $paymentRequest->id,
        'event' => 'transferido',
        'user_id' => $admin->id,
    ]);

    $this->actingAs($creator)
        ->get(PaymentRequestResource::getUrl('edit', ['record' => $paymentRequest]))
        ->assertForbidden();

    $this->get(PaymentRequestResource::getUrl('view', ['record' => $paymentRequest]))
        ->assertOk()
        ->assertSee('Terminado')
        ->assertSee('transferido')
        ->assertDontSee('Save changes');

    expect(PaymentRequestResource::canUpdateRequestDetails($paymentRequest))->toBeFalse();

    $this->actingAs($admin)
        ->get(PaymentRequestResource::getUrl('edit', ['record' => $paymentRequest]))
        ->assertForbidden();

    $this->get(PaymentRequestResource::getUrl('view', ['record' => $paymentRequest]))
        ->assertOk()
        ->assertSee('Terminado')
        ->assertSee('transferido')
        ->assertDontSee('Save changes');

    expect(PaymentRequestResource::canUpdateRequestDetails($paymentRequest))->toBeFalse();
});
