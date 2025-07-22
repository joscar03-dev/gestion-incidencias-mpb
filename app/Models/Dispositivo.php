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
        // Nuevos campos agregados
        'marca',
        'modelo',
        'codigo_activo',
        'etiqueta_inventario',
        'costo_adquisicion',
        'moneda',
        'proveedor',
        'fecha_garantia',
        'tipo_garantia',
        'fecha_instalacion',
        'vida_util_anos',
        'especificaciones_tecnicas',
        'color',
        'tipo_conexion',
        'observaciones',
        'accesorios_incluidos',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_garantia' => 'date',
        'fecha_instalacion' => 'date',
        'especificaciones_tecnicas' => 'array',
        'costo_adquisicion' => 'decimal:2',
        'vida_util_anos' => 'integer',
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

    public function scopeDisponiblesParaAsignacion($query)
    {
        return $query->where('estado', 'Disponible')
            ->whereDoesntHave('asignaciones', function ($subQuery) {
                $subQuery->whereNull('fecha_desasignacion');
            });
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

    // Nuevos métodos para los campos agregados
    public function getInformacionCompletaAttribute()
    {
        $info = [];

        if ($this->marca) $info[] = "Marca: {$this->marca}";
        if ($this->modelo) $info[] = "Modelo: {$this->modelo}";
        if ($this->numero_serie) $info[] = "S/N: {$this->numero_serie}";
        if ($this->codigo_activo) $info[] = "Código: {$this->codigo_activo}";

        return implode(' | ', $info);
    }

    public function getGarantiaVigentAttribute()
    {
        if (!$this->fecha_garantia) return null;

        return $this->fecha_garantia->isFuture();
    }

    public function getDiasRestantesGarantiaAttribute()
    {
        if (!$this->fecha_garantia) return null;

        return max(0, now()->diffInDays($this->fecha_garantia, false));
    }

    public function getVidaUtilRestanteAttribute()
    {
        if (!$this->fecha_instalacion || !$this->vida_util_anos) return null;

        $fechaVencimiento = $this->fecha_instalacion->addYears($this->vida_util_anos);
        return max(0, now()->diffInDays($fechaVencimiento, false));
    }

    public function getEspecificacionPorClaveAttribute()
    {
        return function($clave) {
            if (!$this->especificaciones_tecnicas || !is_array($this->especificaciones_tecnicas)) {
                return null;
            }

            return $this->especificaciones_tecnicas[$clave] ?? null;
        };
    }

    public function getCostoFormateadoAttribute()
    {
        if (!$this->costo_adquisicion) return null;

        return $this->moneda . ' ' . number_format($this->costo_adquisicion, 2);
    }

    public function estaDisponible()
    {
        return $this->estado === 'Disponible';
    }

    public function estaAsignado()
    {
        return $this->estado === 'Asignado' && $this->usuario_id !== null;
    }

    // Scopes adicionales
    public function scopeConGarantiaVigente($query)
    {
        return $query->whereNotNull('fecha_garantia')
                    ->where('fecha_garantia', '>', now());
    }

    public function scopeConGarantiaVencida($query)
    {
        return $query->whereNotNull('fecha_garantia')
                    ->where('fecha_garantia', '<=', now());
    }

    public function scopePorMarca($query, $marca)
    {
        return $query->where('marca', $marca);
    }

    public function scopePorProveedor($query, $proveedor)
    {
        return $query->where('proveedor', $proveedor);
    }

    /**
     * Desasigna el dispositivo, cerrando todas las asignaciones activas
     *
     * @param string|null $motivo Motivo de la desasignación
     * @param int|null $adminId ID del usuario que realiza la desasignación
     * @return bool True si se realizaron desasignaciones, False si no había asignaciones activas
     */
    public function desasignar($motivo = null, $adminId = null)
    {
        $asignacionesActivas = $this->asignaciones()->whereNull('fecha_desasignacion')->get();

        if ($asignacionesActivas->isEmpty()) {
            // No hay asignaciones activas, solo actualizamos el estado
            $this->update([
                'usuario_id' => null,
                'estado' => 'Disponible'
            ]);
            return false;
        }

        foreach ($asignacionesActivas as $asignacion) {
            $asignacion->desasignar($motivo, $adminId);
        }

        // La actualización del dispositivo ya se hace en el método desasignar de DispositivoAsignacion
        // pero lo hacemos aquí también para asegurar consistencia
        $this->refresh(); // Recargamos el modelo por si las actualizaciones en DispositivoAsignacion no se reflejan inmediatamente

        // Verificamos que el usuario_id sea null
        if ($this->usuario_id !== null || $this->estado !== 'Disponible') {
            $this->update([
                'usuario_id' => null,
                'estado' => 'Disponible'
            ]);
        }

        return true;
    }
}
