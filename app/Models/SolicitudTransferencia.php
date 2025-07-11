<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudTransferencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispositivo_id',
        'usuario_origen_id',
        'usuario_destino_id',
        'motivo',
        'estado',
        'fecha_solicitud',
        'fecha_aprobacion',
        'fecha_rechazo',
        'fecha_transferencia',
        'observaciones_admin',
        'aprobado_por',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_rechazo' => 'datetime',
        'fecha_transferencia' => 'datetime',
    ];

    const ESTADOS = [
        'Pendiente' => 'Pendiente',
        'Aprobado' => 'Aprobado',
        'Rechazado' => 'Rechazado',
        'Completado' => 'Completado',
    ];

    // Relaciones
    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class);
    }

    public function usuarioOrigen()
    {
        return $this->belongsTo(User::class, 'usuario_origen_id');
    }

    public function usuarioDestino()
    {
        return $this->belongsTo(User::class, 'usuario_destino_id');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
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
    public function aprobar($adminId, $observaciones = null)
    {
        $this->update([
            'estado' => 'Aprobado',
            'fecha_aprobacion' => now(),
            'aprobado_por' => $adminId,
            'observaciones_admin' => $observaciones,
        ]);
    }

    public function ejecutarTransferencia($adminId)
    {
        // Desasignar del usuario origen
        DispositivoAsignacion::where('dispositivo_id', $this->dispositivo_id)
            ->where('user_id', $this->usuario_origen_id)
            ->whereNull('fecha_desasignacion')
            ->update([
                'fecha_desasignacion' => now(),
                'motivo_desasignacion' => 'Transferencia aprobada',
            ]);

        // Asignar al usuario destino
        DispositivoAsignacion::create([
            'dispositivo_id' => $this->dispositivo_id,
            'user_id' => $this->usuario_destino_id,
            'fecha_asignacion' => now(),
            'asignado_por' => $adminId,
            'motivo' => 'Transferencia desde ' . $this->usuarioOrigen->name,
            'solicitud_transferencia_id' => $this->id,
        ]);

        // Actualizar dispositivo
        $this->dispositivo->update([
            'usuario_id' => $this->usuario_destino_id,
        ]);

        // Marcar transferencia como completada
        $this->update([
            'estado' => 'Completado',
            'fecha_transferencia' => now(),
        ]);
    }

    public function rechazar($adminId, $motivo)
    {
        $this->update([
            'estado' => 'Rechazado',
            'fecha_rechazo' => now(),
            'aprobado_por' => $adminId,
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
}
