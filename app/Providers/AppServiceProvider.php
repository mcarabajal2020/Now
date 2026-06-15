<?php

namespace App\Providers;

use App\Models\TipoCierre;
use App\Models\TipoTarea;
use App\Policies\TipoCierrePolicy;
use App\Policies\TipoTareaPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }

    /**
     * Register model policies.
     *
     * Nota: en este proyecto no existe registerPolicies() en el container
     * (según el error de Artisan). Por eso se deja este método vacío.
     * La asignación de policies debe hacerse en el proveedor correcto.
     */
    protected function registerPolicies(): void
    {
        //
    }
}
