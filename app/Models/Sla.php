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
        'canal',
        'descripcion',
        'activo',
        // Campos para SLA híbrido
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

    /**
     * Factores base para prioridades de tickets
     */
    protected static $factoresPrioridadBase = [
        'critico' => 0.2,   // 20% del tiempo normal - MUY URGENTE
        'critica' => 0.2,   // 20% del tiempo normal (alternativa)
        'urgente' => 0.2,   // 20% del tiempo normal (alternativa)
        'alto' => 0.5,      // 50% del tiempo normal - URGENTE
        'alta' => 0.5,      // 50% del tiempo normal (alternativa)
        'medio' => 1.0,     // 100% del tiempo normal - NORMAL
        'media' => 1.0,     // 100% del tiempo normal (alternativa)
        'bajo' => 1.5,      // 150% del tiempo normal - MENOS URGENTE
        'baja' => 1.5       // 150% del tiempo normal (alternativa)
    ];

    /**
     * Factores base para tipos de tickets
     */
    protected static $factoresTipoBase = [
        'incidente' => 0.6,      // 60% - Los incidentes requieren respuesta rápida
        'general' => 0.8,        // 80% - Consultas generales son importantes
        'requerimiento' => 1.2,  // 120% - Los requerimientos pueden tomar más tiempo
        'cambio' => 1.5          // 150% - Los cambios requieren planificación y más tiempo
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Calcula el SLA efectivo considerando área, prioridad y tipo de ticket
     */
    public function calcularSlaEfectivo($prioridadTicket = null, $tipoTicket = null)
    {
        // Si el override está desactivado, usar SLA del área sin modificaciones
        if (!$this->override_area) {
            return [
                'tiempo_respuesta' => $this->tiempo_respuesta,
                'tiempo_resolucion' => $this->tiempo_resolucion,
                'override_aplicado' => false,
                'factor_prioridad' => 1.0,
                'factor_tipo' => 1.0,
                'factor_combinado' => 1.0
            ];
        }

        // Obtener factores individuales
        $factorPrioridad = $this->obtenerFactorPrioridad($prioridadTicket);
        $factorTipo = $this->obtenerFactorTipo($tipoTicket);

        // Calcular factor combinado
        $factorCombinado = $factorPrioridad * $factorTipo;

        return [
            'tiempo_respuesta' => (int)($this->tiempo_respuesta * $factorCombinado),
            'tiempo_resolucion' => (int)($this->tiempo_resolucion * $factorCombinado),
            'prioridad_aplicada' => $prioridadTicket,
            'tipo_aplicado' => $tipoTicket,
            'override_aplicado' => true,
            'factor_prioridad' => $factorPrioridad,
            'factor_tipo' => $factorTipo,
            'factor_combinado' => $factorCombinado,
            'tiempos_base' => [
                'respuesta' => $this->tiempo_respuesta,
                'resolucion' => $this->tiempo_resolucion
            ]
        ];
    }

    /**
     * Obtiene el factor multiplicador para la prioridad del ticket
     */
    public function obtenerFactorPrioridad($prioridadTicket = null)
    {
        if (!$prioridadTicket) {
            return 1.0;
        }

        $prioridadNormalizada = strtolower($prioridadTicket);
        return self::$factoresPrioridadBase[$prioridadNormalizada] ?? 1.0;
    }

    /**
     * Obtiene el factor multiplicador para el tipo de ticket
     */
    public function obtenerFactorTipo($tipoTicket = null)
    {
        if (!$tipoTicket) {
            return 1.0;
        }

        $tipoNormalizado = strtolower($tipoTicket);
        return self::$factoresTipoBase[$tipoNormalizado] ?? 1.0;
    }

    /**
     * Determina si debe escalar automáticamente
     */
    public function debeEscalar($tiempoTranscurrido, $prioridadTicket = null, $tipoTicket = null)
    {
        if (!$this->escalamiento_automatico || !$this->tiempo_escalamiento) {
            return false;
        }

        // Obtener el SLA efectivo considerando prioridad y tipo
        $slaEfectivo = $this->calcularSlaEfectivo($prioridadTicket, $tipoTicket);

        // Aplicar el mismo factor al tiempo de escalamiento
        $tiempoEscalamientoEfectivo = (int)($this->tiempo_escalamiento * $slaEfectivo['factor_combinado']);

        // Escalar si ha pasado el tiempo de escalamiento efectivo
        return $tiempoTranscurrido >= $tiempoEscalamientoEfectivo;
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

    /**
     * Obtiene todos los factores de prioridad disponibles
     */
    public static function getFactoresPrioridad()
    {
        return [
            'critico' => ['factor' => self::$factoresPrioridadBase['critico'], 'label' => 'Crítica', 'descripcion' => 'Muy Urgente - 20% del tiempo'],
            'alto' => ['factor' => self::$factoresPrioridadBase['alto'], 'label' => 'Alta', 'descripcion' => 'Urgente - 50% del tiempo'],
            'medio' => ['factor' => self::$factoresPrioridadBase['medio'], 'label' => 'Media', 'descripcion' => 'Normal - 100% del tiempo'],
            'bajo' => ['factor' => self::$factoresPrioridadBase['bajo'], 'label' => 'Baja', 'descripcion' => 'Menos Urgente - 150% del tiempo'],
        ];
    }

    /**
     * Obtiene todos los factores de tipo disponibles
     */
    public static function getFactoresTipo()
    {
        return [
            'incidente' => ['factor' => self::$factoresTipoBase['incidente'], 'label' => 'Incidente', 'descripcion' => 'Respuesta rápida - 60% del tiempo'],
            'general' => ['factor' => self::$factoresTipoBase['general'], 'label' => 'General', 'descripcion' => 'Consulta importante - 80% del tiempo'],
            'requerimiento' => ['factor' => self::$factoresTipoBase['requerimiento'], 'label' => 'Requerimiento', 'descripcion' => 'Planificación - 120% del tiempo'],
            'cambio' => ['factor' => self::$factoresTipoBase['cambio'], 'label' => 'Cambio', 'descripcion' => 'Requiere análisis - 150% del tiempo'],
        ];
    }

    /**
     * Calcula un ejemplo de SLA para mostrar en la interfaz
     */
    public function calcularEjemploSla($prioridad, $tipo)
    {
        $sla = $this->calcularSlaEfectivo($prioridad, $tipo);

        return [
            'respuesta_horas' => round($sla['tiempo_respuesta'] / 60, 1),
            'resolucion_horas' => round($sla['tiempo_resolucion'] / 60, 1),
            'respuesta_minutos' => $sla['tiempo_respuesta'],
            'resolucion_minutos' => $sla['tiempo_resolucion'],
            'factor_combinado' => $sla['factor_combinado'],
            'descripcion' => ucfirst($prioridad) . ' + ' . ucfirst($tipo)
        ];
    }

    /**
     * Método privado para obtener el SLA activo de un área
     */
    private static function getSlaActivoPorArea($areaId)
    {
        return static::where('area_id', $areaId)
                    ->where('activo', true)
                    ->first();
    }

    /**
     * Obtiene el SLA de un área y calcula los tiempos efectivos
     * Este es el método principal para usar el sistema híbrido
     */
    public static function calcularParaTicket($areaId, $prioridadTicket = null, $tipoTicket = null)
    {
        $sla = self::getSlaActivoPorArea($areaId);

        if (!$sla) {
            return [
                'encontrado' => false,
                'mensaje' => 'No se encontró SLA para esta área',
                'tiempo_respuesta' => null,
                'tiempo_resolucion' => null
            ];
        }

        $tiemposCalculados = $sla->calcularSlaEfectivo($prioridadTicket, $tipoTicket);

        return array_merge([
            'encontrado' => true,
            'sla_id' => $sla->id,
            'area_nombre' => $sla->area->nombre ?? 'Área no encontrada'
        ], $tiemposCalculados);
    }

    /**
     * Método helper para verificar si un ticket debe escalar
     */
    public static function verificarEscalamiento($areaId, $tiempoTranscurrido, $prioridadTicket = null, $tipoTicket = null)
    {
        $sla = self::getSlaActivoPorArea($areaId);

        if (!$sla) {
            return false;
        }

        return $sla->debeEscalar($tiempoTranscurrido, $prioridadTicket, $tipoTicket);
    }
}
