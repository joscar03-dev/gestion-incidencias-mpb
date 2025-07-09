<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;
use Kirschbaum\Commentions\Contracts\Commenter;

class User extends Authenticatable implements FilamentUser, Commenter
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'area_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    /**
     * Método para verificar si el usuario tiene un rol específico.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole(['Super Admin','Admin', 'Técnico']);
        }

        if ($panel->getId() === 'user') {
            return $this->hasRole(['Super Admin','Usuario', 'Admin','Técnico']);
        }

        return false;
    }
    /**
     * Método para verificar si el usuario es un administrador.
     */
    public function ticketsAsignados()
    {
        return $this->hasMany(Ticket::class, 'asignado_a');
    }
    /**
     * Relación con los tickets creados por el usuario.
     */
    public function ticketsCreados()
    {
        return $this->hasMany(Ticket::class, 'creado_por');
    }
    /**
     * Relación con el área del usuario.
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
    /**
     * Relación con los dispositivos asignados al usuario.
     */
    public function dispositivos()
    {
        return $this->hasMany(Dispositivo::class, 'usuario_id');
    }

    public function dispositivosAsignados()
    {
        return $this->hasMany(\App\Models\DispositivoAsignacion::class);
    }
}
