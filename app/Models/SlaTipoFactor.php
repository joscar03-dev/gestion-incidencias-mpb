<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaTipoFactor extends Model
{
    use HasFactory;

    protected $table = 'sla_tipo_factores';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'factor',
        'activo',
        'orden'
    ];

    protected $casts = [
        'factor' => 'decimal:2',
        'activo' => 'boolean',
        'orden' => 'integer'
    ];

    /**
     * Scope para obtener solo factores activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para ordenar por orden configurado
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    /**
     * Obtener factor por código
     */
    public static function obtenerFactorPorCodigo($codigo)
    {
        if (!$codigo) {
            return 1.0;
        }

        $factor = static::activos()
            ->where('codigo', strtolower($codigo))
            ->first();

        return $factor ? (float)$factor->factor : 1.0;
    }

    /**
     * Obtener todos los factores como array para compatibilidad
     */
    public static function obtenerFactoresArray()
    {
        return static::activos()
            ->ordenados()
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->codigo => [
                        'factor' => (float)$item->factor,
                        'label' => $item->nombre,
                        'descripcion' => $item->descripcion
                    ]
                ];
            })
            ->toArray();
    }
}
