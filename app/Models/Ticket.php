<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TicketComment;
use Kirschbaum\Commentions\Contracts\Commentable;
use Kirschbaum\Commentions\HasComments;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Filament\Notifications\Events\DatabaseNotificationsSent;

class Ticket extends Model implements Commentable
{
    use HasComments;
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'prioridad',
        'comentario',
        'asignado_a',
        'asignado_por',
        'creado_por',
        'is_resolved',
        'attachment',
        'tiempo_respuesta',
        'tiempo_solucion',
        'fecha_cierre',
        'escalado',
        'fecha_escalamiento',
        'sla_vencido',
        'area_id', // Agregar relación directa con área
    ];

    const PRIORIDAD =
    [
        'Critica' => 'Critica',
        'Alta' => 'Alta',
        'Media' => 'Media',
        'Baja' => 'Baja',
    ];

    const ESTADOS =
    [
        'Abierto' => 'Abierto',
        'En Progreso' => 'En Progreso',
        'Escalado' => 'Escalado',
        'Cerrado' => 'Cerrado',
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
                $ticket->estado === self::ESTADOS['Cerrado']
            ) {
                $ticket->fecha_cierre = now();
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
        // No escalar si ya fue escalado o está cerrado
        if ($this->escalado || $this->estado === self::ESTADOS['Cerrado']) {
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
        // Obtener usuarios a notificar
        $usuariosNotificar = $this->obtenerUsuariosParaNotificar();

        // Enviar notificaciones personalizadas
        foreach ($usuariosNotificar as $usuario) {
            $this->enviarNotificacionPersonalizada($usuario, $motivo, $prioridadAnterior, $nuevaPrioridad);
        }

        // Agregar comentario al ticket
        $this->agregarComentarioEscalado($motivo, $prioridadAnterior, $nuevaPrioridad);
    }

    /**
     * Envía notificación personalizada según el tipo de usuario
     */
    private function enviarNotificacionPersonalizada($usuario, $motivo, $prioridadAnterior, $nuevaPrioridad)
    {
        try {
            // Determinar el tipo de usuario
            $esAdmin = $usuario->hasRole(['admin', 'super_admin']);
            $esTecnicoAsignado = $this->asignado_a === $usuario->id;

            // Personalizar mensaje según el tipo de usuario
            if ($esTecnicoAsignado) {
                $titulo = "🚨 Tu ticket #{$this->id} fue escalado";
                $mensaje = "Tu ticket '{$this->titulo}' ha sido escalado automáticamente debido a que se venció el SLA.\n\n" .
                          "⚠️ Acción requerida: Por favor, revisa el ticket inmediatamente.\n\n" .
                          "📋 Detalles del escalado:\n" .
                          "• Motivo: {$motivo}\n" .
                          "• Prioridad: {$prioridadAnterior} → {$nuevaPrioridad}\n" .
                          "• Área: {$this->area->nombre}\n" .
                          "• Escalado: " . now()->format('d/m/Y H:i') . "\n\n" .
                          "🔗 Revisa el ticket en el panel de administración.";
            } else {
                $titulo = "🚨 Ticket #{$this->id} escalado - Supervisión requerida";
                $mensaje = "El ticket '{$this->titulo}' ha sido escalado automáticamente y requiere supervisión.\n\n" .
                          "📋 Detalles del escalado:\n" .
                          "• Motivo: {$motivo}\n" .
                          "• Prioridad: {$prioridadAnterior} → {$nuevaPrioridad}\n" .
                          "• Área: {$this->area->nombre}\n" .
                          "• Técnico asignado: {$this->asignadoA->name}\n" .
                          "• Creado: {$this->created_at->format('d/m/Y H:i')}\n" .
                          "• Escalado: " . now()->format('d/m/Y H:i') . "\n\n" .
                          "🔗 Revisa el ticket y toma las acciones necesarias.";
            }

            // Enviar notificación de Filament
            Notification::make()
                ->title($titulo)
                ->body($mensaje)
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('warning')
                ->persistent() // Notificación persistente para escalados
                ->sendToDatabase($usuario);

            // Disparar evento para actualizar la UI
            event(new DatabaseNotificationsSent($usuario));

            // Log para debugging
            Log::info("Notificación de escalado enviada", [
                'usuario' => $usuario->name,
                'email' => $usuario->email,
                'ticket_id' => $this->id,
                'tipo_usuario' => $esTecnicoAsignado ? 'tecnico_asignado' : 'admin',
                'titulo' => $titulo
            ]);

        } catch (\Exception $e) {
            Log::error("Error al enviar notificación de escalado", [
                'usuario' => $usuario->id,
                'ticket_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene los usuarios que deben ser notificados del escalado
     */
    private function obtenerUsuariosParaNotificar()
    {
        $usuarios = collect();

        // 1. Técnico asignado
        if ($this->asignadoA) {
            $usuarios->push($this->asignadoA);
        }

        // 2. Admin y Superadmin
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'super_admin']);
        })->get();

        $usuarios = $usuarios->merge($adminUsers);

        // Remover duplicados
        return $usuarios->unique('id');
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
                         "• Notificados: Técnico asignado, Admin y Superadmin\n" .
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
        if (in_array($this->estado, [self::ESTADOS['Cerrado'], self::ESTADOS['Archivado']])) {
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
     * Scopes para consultas frecuentes
     */
    public function scopeActivos($query)
    {
        return $query->whereNotIn('estado', [self::ESTADOS['Cerrado'], self::ESTADOS['Archivado']]);
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
}
