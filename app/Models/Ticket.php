<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TicketComment;
use Kirschbaum\Commentions\Contracts\Commentable;
use Kirschbaum\Commentions\HasComments;
use Illuminate\Support\Facades\Log;

class Ticket extends Model implements Commentable
{
    use HasComments;
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'prioridad',
        'tipo', // Agregar tipo de ticket
        'comentario',
        'asignado_a',
        'asignado_por',
        'creado_por',
        'is_resolved',
        'attachment',
        'tiempo_respuesta',
        'tiempo_solucion',
        'fecha_cierre',
        'fecha_resolucion',
        'comentarios_resolucion',
        'escalado',
        'fecha_escalamiento',
        'sla_vencido',
        'area_id', // Agregar relación directa con área
        'dispositivo_id', // Agregar relación con dispositivo
    ];

    protected $casts = [
        'fecha_resolucion' => 'datetime',
        'fecha_cierre' => 'datetime',
        'fecha_escalamiento' => 'datetime',
        'escalado' => 'boolean',
        'sla_vencido' => 'boolean',
        'is_resolved' => 'boolean',
    ];

    const PRIORIDAD =
    [
        'Critica' => 'Critica',
        'Alta' => 'Alta',
        'Media' => 'Media',
        'Baja' => 'Baja',
    ];

    const TIPOS =
    [
        'Incidente' => 'Incidente',
        'General' => 'General',
        'Requerimiento' => 'Requerimiento',
        'Cambio' => 'Cambio',
    ];

    const ESTADOS =
    [
        'Abierto' => 'Abierto',
        'En Progreso' => 'En Progreso',
        'Escalado' => 'Escalado',
        'Cerrado' => 'Cerrado',
        'Cancelado' => 'Cancelado',
        'Archivado' => 'Archivado',
    ];

    public function asignadoA()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function asignadoPor()
    {
        return $this->belongsTo(User::class, 'asignado_por');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    // Relación directa con Area (mejorada)
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    // Relación con Dispositivo
    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id');
    }

    // Mantener compatibilidad con el getter anterior
    public function getAreaAttribute()
    {
        // Primero verificar si tiene área asignada directamente
        if ($this->attributes['area_id']) {
            return $this->area()->first();
        }
        // Si no, usar el área del usuario que lo creó
        return $this->creadoPor ? $this->creadoPor->area : null;
    }

    public function sla()
    {
        return $this->belongsTo(Sla::class, 'sla_id');
    }

    protected static function booted()
    {
        // Al crear un ticket, asignar área automáticamente si no tiene
        static::creating(function ($ticket) {
            if (!$ticket->area_id && $ticket->creadoPor) {
                $ticket->area_id = $ticket->creadoPor->area_id;
            }
        });

        static::updating(function ($ticket) {
            if (
                $ticket->isDirty('estado') &&
                ($ticket->estado === self::ESTADOS['Cerrado'] || $ticket->estado === self::ESTADOS['Cancelado'])
            ) {
                $ticket->fecha_cierre = now();
                // Si no tiene fecha_resolucion, asignarla también
                if (!$ticket->fecha_resolucion) {
                    $ticket->fecha_resolucion = now();
                }
            }
        });

        // Verificar SLA después de crear o actualizar
        // Comentado para evitar escalado automático en cada guardado
        // El escalado debe ejecutarse solo mediante jobs programados o comandos manuales
        // static::saved(function ($ticket) {
        //     $ticket->verificarSlaYEscalamiento();
        // });
    }

    public function getTiempoResolucionRealAttribute()
    {
        if (!$this->fecha_cierre) {
            return null;
        }
        $inicio = $this->created_at;
        $fin = $this->fecha_cierre;
        $diff = $inicio->diff($fin);
        return sprintf('%d horas, %d minutos', ($diff->days * 24) + $diff->h, $diff->i);
    }

    /**
     * Obtiene el SLA efectivo considerando área y prioridad del ticket
     */
    public function getSlaEfectivo()
    {
        $area = $this->area;
        if (!$area || $area->slas->isEmpty()) {
            return null;
        }

        $slaArea = $area->slas->first();

        // Mapear prioridad del ticket al formato esperado por el SLA
        $prioridadParaSla = match($this->prioridad) {
            'Critica' => 'critico',
            'Alta' => 'alto',
            'Media' => 'medio',
            'Baja' => 'bajo',
            default => 'medio'
        };

        // Usar el método del SLA que respeta la configuración de override
        $slaCalculado = $slaArea->calcularSlaEfectivo($prioridadParaSla);

        return [
            'tiempo_respuesta' => $slaCalculado['tiempo_respuesta'],
            'tiempo_resolucion' => $slaCalculado['tiempo_resolucion'],
            'prioridad_aplicada' => $this->prioridad,
            'factor_aplicado' => $slaCalculado['factor_aplicado'] ?? 1.0,
            'override_aplicado' => $slaCalculado['override_aplicado'] ?? false,
            'sla_base' => $slaArea
        ];
    }

    /**
     * Verifica si el ticket está vencido según su SLA efectivo
     */
    public function estaVencido($tipo = 'respuesta')
    {
        $slaEfectivo = $this->getSlaEfectivo();
        if (!$slaEfectivo) {
            return false;
        }

        $tiempoTranscurrido = abs(now()->diffInMinutes($this->created_at));
        $tiempoLimite = $tipo === 'respuesta'
            ? $slaEfectivo['tiempo_respuesta']
            : $slaEfectivo['tiempo_resolucion'];

        return $tiempoTranscurrido > $tiempoLimite;
    }

    /**
     * Verifica si el ticket debe ser escalado
     */
    public function debeEscalar()
    {
        // No escalar si ya fue escalado o está cerrado/cancelado
        if ($this->escalado || in_array($this->estado, [self::ESTADOS['Cerrado'], self::ESTADOS['Cancelado']])) {
            return false;
        }

        $area = $this->area;
        if (!$area || $area->slas->isEmpty()) {
            return false;
        }

        $slaArea = $area->slas->first();

        // Usar el método del SLA que considera la configuración de escalamiento automático
        $tiempoTranscurrido = $this->created_at->diffInMinutes(now());

        // Mapear prioridad del ticket al formato esperado por el SLA
        $prioridadParaSla = match($this->prioridad) {
            'Critica' => 'critico',
            'Alta' => 'alto',
            'Media' => 'medio',
            'Baja' => 'bajo',
            default => 'medio'
        };

        return $slaArea->debeEscalar($tiempoTranscurrido, $prioridadParaSla);
    }

    /**
     * Escala el ticket automáticamente
     */
    public function escalar($motivo = 'SLA vencido')
    {
        $prioridadAnterior = $this->prioridad;
        $nuevaPrioridad = $this->incrementarPrioridad();

        $this->update([
            'escalado' => true,
            'fecha_escalamiento' => now(),
            'estado' => self::ESTADOS['Escalado'],
            'prioridad' => $nuevaPrioridad
        ]);

        // Notificar el escalamiento
        $this->notificarEscalamiento($motivo, $prioridadAnterior, $nuevaPrioridad);

        return true;
    }

    /**
     * Incrementa la prioridad del ticket al escalarse
     */
    private function incrementarPrioridad()
    {
        $escalaPrioridad = [
            'Baja' => 'Media',
            'Media' => 'Alta',
            'Alta' => 'Critica',
            'Critica' => 'Critica' // Ya está al máximo
        ];

        return $escalaPrioridad[$this->prioridad] ?? 'Media';
    }    /**
     * Notifica el escalamiento a los usuarios correspondientes
     */
    private function notificarEscalamiento($motivo, $prioridadAnterior, $nuevaPrioridad)
    {
        // Agregar comentario al ticket
        $this->agregarComentarioEscalado($motivo, $prioridadAnterior, $nuevaPrioridad);

        // Log del escalamiento
        Log::info("Ticket escalado", [
            'ticket_id' => $this->id,
            'motivo' => $motivo,
            'prioridad_anterior' => $prioridadAnterior,
            'nueva_prioridad' => $nuevaPrioridad,
            'area' => $this->area->nombre ?? 'Sin área'
        ]);
    }

    /**
     * Agrega un comentario al ticket sobre el escalado
     */
    private function agregarComentarioEscalado($motivo, $prioridadAnterior, $nuevaPrioridad)
    {
        try {
            $comentario = "🚨 ESCALADO AUTOMÁTICO\n\n" .
                         "• Motivo: {$motivo}\n" .
                         "• Prioridad cambió de {$prioridadAnterior} a {$nuevaPrioridad}\n" .
                         "• Fecha: " . now()->format('d/m/Y H:i') . "\n" .
                         "• Sistema: Escalado automático por vencimiento de SLA";

            $this->comment($comentario, $this->creadoPor);

        } catch (\Exception $e) {
            Log::error("Error al agregar comentario de escalado", [
                'ticket_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene el tiempo restante para vencimiento del SLA
     */
    public function getTiempoRestanteSla($tipo = 'respuesta')
    {
        $slaEfectivo = $this->getSlaEfectivo();
        if (!$slaEfectivo) {
            return null;
        }

        $tiempoTranscurrido = abs(now()->diffInMinutes($this->created_at));
        $tiempoLimite = $tipo === 'respuesta'
            ? $slaEfectivo['tiempo_respuesta']
            : $slaEfectivo['tiempo_resolucion'];

        $tiempoRestante = $tiempoLimite - $tiempoTranscurrido;

        return $tiempoRestante > 0 ? $tiempoRestante : 0;
    }

    /**
     * Obtiene el estado del SLA (OK, Advertencia, Vencido)
     */
    public function getEstadoSla()
    {
        if (!$this->getSlaEfectivo()) {
            return 'sin_sla';
        }

        $tiempoRestanteRespuesta = $this->getTiempoRestanteSla('respuesta');
        $tiempoRestanteResolucion = $this->getTiempoRestanteSla('resolucion');

        if ($this->estaVencido('respuesta')) {
            return 'vencido';
        }

        // Advertencia si queda menos del 25% del tiempo
        $slaEfectivo = $this->getSlaEfectivo();
        $umbralAdvertencia = $slaEfectivo['tiempo_respuesta'] * 0.25;

        if ($tiempoRestanteRespuesta <= $umbralAdvertencia) {
            return 'advertencia';
        }

        return 'ok';
    }

    /**
     * Verifica SLA y ejecuta escalamiento si es necesario
     */
    public function verificarSlaYEscalamiento()
    {
        // Solo verificar si el ticket está abierto o en progreso
        if (in_array($this->estado, [self::ESTADOS['Cerrado'], self::ESTADOS['Cancelado'], self::ESTADOS['Archivado']])) {
            return false;
        }

        // Marcar como vencido si corresponde
        if ($this->estaVencido('respuesta') && !$this->sla_vencido) {
            $this->update(['sla_vencido' => true]);
        }

        // Verificar si debe escalar
        if ($this->debeEscalar()) {
            $this->escalar();
            return true;
        }

        return false;
    }

    /**
     * Calcula el SLA efectivo para este ticket usando el sistema híbrido
     */
    public function calcularSlaEfectivo()
    {
        if (!$this->area_id) {
            return [
                'encontrado' => false,
                'mensaje' => 'Ticket sin área asignada',
                'tiempo_respuesta' => null,
                'tiempo_resolucion' => null
            ];
        }

        return Sla::calcularParaTicket(
            $this->area_id,
            $this->prioridad,
            $this->tipo
        );
    }

    /**
     * Verifica si este ticket debe escalar automáticamente
     */
    public function debeEscalarAutomaticamente()
    {
        if (!$this->area_id || !$this->created_at) {
            return false;
        }

        $tiempoTranscurrido = $this->created_at->diffInMinutes(now());

        return Sla::verificarEscalamiento(
            $this->area_id,
            $tiempoTranscurrido,
            $this->prioridad,
            $this->tipo
        );
    }

    /**
     * Obtiene los tiempos de SLA calculados para este ticket
     */
    public function getTiemposSlaAttribute()
    {
        return $this->calcularSlaEfectivo();
    }

    /**
     * Scopes para consultas frecuentes
     */
    public function scopeActivos($query)
    {
        return $query->whereNotIn('estado', [self::ESTADOS['Cerrado'], self::ESTADOS['Cancelado'], self::ESTADOS['Archivado']]);
    }

    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    public function scopeVencidos($query)
    {
        return $query->where('sla_vencido', true);
    }

    public function scopeEscalados($query)
    {
        return $query->where('escalado', true);
    }

    public function scopeCriticos($query)
    {
        return $query->where('prioridad', 'Critica');
    }

    public function scopePorArea($query, $areaId)
    {
        return $query->where('area_id', $areaId);
    }

    /**
     * Asignar automáticamente un técnico disponible basado en la carga de trabajo
     *
     * @return User|null El técnico asignado o null si no hay técnicos disponibles
     */
    public static function asignarTecnicoAutomaticamente()
    {
        // Estados que no se consideran como carga de trabajo activa
        $estadosNoDisponibles = [
            self::ESTADOS['Cerrado'],
            self::ESTADOS['Archivado'],
        ];

        // Buscar usuarios con rol 'Técnico' o 'Tecnico' (por compatibilidad)
        $tecnicos = User::role(['Técnico', 'Tecnico'])->get();

        if ($tecnicos->isEmpty()) {
            return null;
        }

        // Filtrar técnicos disponibles (<5 tickets no cerrados ni archivados)
        $disponibles = $tecnicos->filter(function ($u) use ($estadosNoDisponibles) {
            return $u->ticketsAsignados()
                ->whereNotIn('estado', $estadosNoDisponibles)
                ->count() < 5;
        });

        if ($disponibles->isEmpty()) {
            // Si no hay técnicos disponibles, seleccionar aleatoriamente cualquier técnico
            return $tecnicos->random();
        } else {
            // Seleccionar el técnico con menor número de tickets no cerrados ni archivados
            return $disponibles->sortBy(function ($u) use ($estadosNoDisponibles) {
                return $u->ticketsAsignados()
                    ->whereNotIn('estado', $estadosNoDisponibles)
                    ->count();
            })->first();
        }
    }
}
