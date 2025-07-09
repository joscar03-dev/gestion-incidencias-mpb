<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Area;
use App\Models\Role;
use App\Models\User;
use App\Models\Categoria;
use App\Models\CategoriaDispositivo;
use App\Models\Dispositivo;
use App\Models\DispositivoAsignacion;
use App\Models\Locale;
use App\Models\Permission;
use App\Models\Sla;
use App\Models\Ticket;
use App\Policies;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\CategoriaPolicy;
use App\Policies\CategoriaDispositivoPolicy;
use App\Policies\DispositivoPolicy;
use App\Policies\DispositivoAsignacionPolicy;
use App\Policies\LocalPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\SlaPolicy;
use App\Policies\TicketPolicy;
use App\Policies\AreaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Categoria::class => CategoriaPolicy::class,
        Permission::class => PermissionPolicy::class,
        Area::class => AreaPolicy::class,
        CategoriaDispositivo::class => CategoriaDispositivoPolicy::class,
        Dispositivo::class => DispositivoPolicy::class,
        DispositivoAsignacion::class => DispositivoAsignacionPolicy::class,
        Sla::class => SlaPolicy::class,
        Ticket::class => TicketPolicy::class,
        Locale::class => LocalPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
