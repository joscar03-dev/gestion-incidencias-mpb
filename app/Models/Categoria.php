<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'tipo_categoria',
        'itil_category',
        'prioridad_default',
        'sla_horas',
        'color',
        'icono',
        'is_active'
    ];

    protected $casts = [
        'itil_category' => 'boolean',
        'is_active' => 'boolean',
        'sla_horas' => 'integer',
    ];

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class);
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeItil($query)
    {
        return $query->where('itil_category', true);
    }

    public function scopeByTipo($query, $tipo)
    {
        return $query->where('tipo_categoria', $tipo);
    }

    public function getPrioridadColorAttribute()
    {
        $colors = [
            'baja' => '#10B981',     // Verde
            'media' => '#F59E0B',    // Amarillo
            'alta' => '#EF4444',     // Rojo
            'critica' => '#7C2D12'   // Rojo oscuro
        ];

        return $colors[$this->prioridad_default] ?? '#6B7280';
    }

    public function getSlaStatusAttribute()
    {
        return [
            'horas' => $this->sla_horas,
            'tipo' => $this->sla_horas <= 4 ? 'urgente' : ($this->sla_horas <= 24 ? 'normal' : 'extendido')
        ];
    }
}
