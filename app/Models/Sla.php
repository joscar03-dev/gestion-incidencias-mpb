<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Sla extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'nivel',
        'tiempo_respuesta',
        'tiempo_resolucion',
        'tipo_ticket',
        'canal',
        'descripcion',
        'activo',
        // Nuevos campos para SLA híbrido
        'prioridad_ticket',  // critico, alto, medio, bajo
        'override_area',     // si el ticket puede sobrescribir la prioridad del área
        'escalamiento_automatico', // si escala automáticamente
        'tiempo_escalamiento', // minutos para escalamiento
    ];

    protected $casts = [
        'activo' => 'boolean',
        'override_area' => 'boolean',
        'escalamiento_automatico' => 'boolean',
        'tiempo_respuesta' => 'integer',
        'tiempo_resolucion' => 'integer',
        'tiempo_escalamiento' => 'integer',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Calcula el SLA efectivo considerando área y prioridad del ticket
     */
    public function calcularSlaEfectivo($prioridadTicket = null)
    {
        // Si no hay prioridad de ticket o el override está desactivado, usar SLA del área
        if (!$prioridadTicket || !$this->override_area) {
            return [
                'tiempo_respuesta' => $this->tiempo_respuesta,
                'tiempo_resolucion' => $this->tiempo_resolucion,
                'override_aplicado' => false
            ];
        }

        // Mapeo de prioridades a factores de tiempo (normalizar a minúsculas)
        $factoresPrioridad = [
            'critico' => 0.2,   // 20% del tiempo normal
            'critica' => 0.2,   // 20% del tiempo normal (alternativa)
            'urgente' => 0.2,   // 20% del tiempo normal (alternativa)
            'alto' => 0.5,      // 50% del tiempo normal
            'alta' => 0.5,      // 50% del tiempo normal (alternativa)
            'medio' => 1.0,     // 100% del tiempo normal
            'media' => 1.0,     // 100% del tiempo normal (alternativa)
            'bajo' => 1.5,      // 150% del tiempo normal
            'baja' => 1.5       // 150% del tiempo normal (alternativa)
        ];

        $prioridadNormalizada = strtolower($prioridadTicket);
        $factor = $factoresPrioridad[$prioridadNormalizada] ?? 1.0;

        return [
            'tiempo_respuesta' => (int)($this->tiempo_respuesta * $factor),
            'tiempo_resolucion' => (int)($this->tiempo_resolucion * $factor),
            'prioridad_aplicada' => $prioridadTicket,
            'prioridad_normalizada' => $prioridadNormalizada,
            'override_aplicado' => true,
            'factor_aplicado' => $factor
        ];
    }

    /**
     * Determina si debe escalar automáticamente
     */
    public function debeEscalar($tiempoTranscurrido, $prioridadTicket = null)
    {
        if (!$this->escalamiento_automatico || !$this->tiempo_escalamiento) {
            return false;
        }

        // Obtener el SLA efectivo considerando la configuración de override
        $slaEfectivo = $this->calcularSlaEfectivo($prioridadTicket);

        // Escalar si ha pasado el tiempo de escalamiento configurado
        return $tiempoTranscurrido >= $this->tiempo_escalamiento;
    }

    /**
     * Boot del modelo para validaciones
     */
    protected static function boot()
    {
        parent::boot();

        // Validaciones deshabilitadas temporalmente para usar validación de Filament
        // La validación se maneja en el formulario SlaResource

        // static::creating(function ($sla) {
        //     static::validateUniqueAreaSla($sla);
        // });

        // static::updating(function ($sla) {
        //     static::validateUniqueAreaSla($sla);
        // });
    }

    /**
     * Validar que cada área tenga solo un SLA
     */
    protected static function validateUniqueAreaSla($sla)
    {
        // Solo validar si el area_id está presente y no es null
        if (!$sla->area_id) {
            return;
        }

        $query = static::where('area_id', $sla->area_id);

        // Si el modelo ya existe (estamos actualizando), excluir el registro actual
        if ($sla->exists && $sla->id) {
            $query->where('id', '!=', $sla->id);
        }

        $existingSla = $query->first();

        if ($existingSla) {
            $areaName = Area::find($sla->area_id)?->nombre ?? 'esta área';
            throw ValidationException::withMessages([
                'area_id' => "Ya existe un SLA para {$areaName}. Cada área puede tener únicamente un SLA."
            ]);
        }
    }
}
