<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudDispositivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'categoria_dispositivo_id',
        'justificacion',
        'documento_requerimiento',
        'prioridad',
        'estado',
        'fecha_solicitud',
        'fecha_respuesta',
        'fecha_aprobacion',
        'fecha_rechazo',
        'observaciones_admin',
        'admin_respuesta_id',
        'dispositivo_asignado_id',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_respuesta' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_rechazo' => 'datetime',
    ];

    const ESTADOS = [
        'Pendiente' => 'Pendiente',
        'Aprobado' => 'Aprobado',
        'Rechazado' => 'Rechazado',
        'Completado' => 'Completado',
    ];

    const PRIORIDADES = [
        'Alta' => 'Alta',
        'Media' => 'Media',
        'Baja' => 'Baja',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoria_dispositivo()
    {
        return $this->belongsTo(CategoriaDispositivo::class, 'categoria_dispositivo_id');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'admin_respuesta_id');
    }

    public function dispositivoAsignado()
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_asignado_id');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'Pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'Aprobado');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'Rechazado');
    }

    // Métodos
    public function aprobar($adminId, $observaciones = null, $dispositivoId = null)
    {
        $this->update([
            'estado' => 'Aprobado',
            'fecha_aprobacion' => now(),
            'admin_respuesta_id' => $adminId,
            'observaciones_admin' => $observaciones,
            'dispositivo_asignado_id' => $dispositivoId,
        ]);

        // Si se asigna un dispositivo específico, actualizar su estado
        if ($dispositivoId) {
            Dispositivo::find($dispositivoId)->update([
                'usuario_id' => $this->user_id,
                'estado' => 'Asignado',
            ]);

            // Crear registro de asignación
            DispositivoAsignacion::create([
                'dispositivo_id' => $dispositivoId,
                'user_id' => $this->user_id,
                'fecha_asignacion' => now(),
                'asignado_por' => $adminId,
                'motivo_asignacion' => 'Requerimiento aprobado',
                'solicitud_dispositivo_id' => $this->id,
            ]);

            $this->update(['estado' => 'Completado']);
        }
    }

    public function rechazar($adminId, $motivo)
    {
        $this->update([
            'estado' => 'Rechazado',
            'fecha_rechazo' => now(),
            'admin_respuesta_id' => $adminId,
            'observaciones_admin' => $motivo,
        ]);
    }

    public function getEstadoBadgeColorAttribute()
    {
        return match($this->estado) {
            'Pendiente' => 'warning',
            'Aprobado' => 'info',
            'Completado' => 'success',
            'Rechazado' => 'danger',
            default => 'secondary',
        };
    }

    public function getPrioridadBadgeColorAttribute()
    {
        return match($this->prioridad) {
            'Alta' => 'danger',
            'Media' => 'warning',
            'Baja' => 'success',
            default => 'secondary',
        };
    }
}
