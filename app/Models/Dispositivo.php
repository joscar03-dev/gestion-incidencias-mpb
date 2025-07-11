<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
        'categoria_id',
        'numero_serie',
        'estado',
        'area_id',
        'usuario_id',
        'fecha_compra',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
    ];

    const ESTADOS = [
        'Disponible' => 'Disponible',
        'Asignado' => 'Asignado',
        'Reparación' => 'Reparación',
        'Dañado' => 'Dañado',
        'Retirado' => 'Retirado',
    ];

    public function categoria_dispositivo()
    {
        return $this->belongsTo(CategoriaDispositivo::class, 'categoria_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function asignaciones()
    {
        return $this->hasMany(\App\Models\DispositivoAsignacion::class);
    }

    // Scopes
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'Disponible');
    }

    public function scopeAsignados($query)
    {
        return $query->where('estado', 'Asignado');
    }

    public function scopeEnReparacion($query)
    {
        return $query->where('estado', 'Reparación');
    }

    // Métodos auxiliares
    public function getEstadoBadgeColorAttribute()
    {
        return match($this->estado) {
            'Disponible' => 'success',
            'Asignado' => 'info',
            'Reparación' => 'warning',
            'Dañado' => 'danger',
            'Retirado' => 'secondary',
            default => 'primary',
        };
    }

    public function getImagenUrlAttribute()
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : asset('images/default-device.png');
    }

    public function getAsignacionActivaAttribute()
    {
        return $this->asignaciones()->whereNull('fecha_desasignacion')->first();
    }

    public function estaDisponible()
    {
        return $this->estado === 'Disponible';
    }

    public function estaAsignado()
    {
        return $this->estado === 'Asignado' && $this->usuario_id !== null;
    }
}
