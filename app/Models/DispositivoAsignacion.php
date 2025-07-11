<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispositivoAsignacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispositivo_id',
        'user_id',
        'fecha_asignacion',
        'fecha_desasignacion',
        'asignado_por',
        'motivo_asignacion',
        'motivo_desasignacion',
        'confirmado',
        'fecha_confirmacion',
        'solicitud_dispositivo_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_desasignacion' => 'datetime',
        'fecha_confirmacion' => 'datetime',
        'confirmado' => 'boolean',
    ];

    // Relaciones
    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asignadoPor()
    {
        return $this->belongsTo(User::class, 'asignado_por');
    }

    public function solicitudDispositivo()
    {
        return $this->belongsTo(SolicitudDispositivo::class);
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->whereNull('fecha_desasignacion');
    }

    public function scopeFinalizadas($query)
    {
        return $query->whereNotNull('fecha_desasignacion');
    }

    public function scopePendientesConfirmacion($query)
    {
        return $query->where('confirmado', false)->whereNull('fecha_desasignacion');
    }

    // Métodos
    public function desasignar($motivo = null, $adminId = null)
    {
        $this->update([
            'fecha_desasignacion' => now(),
            'motivo_desasignacion' => $motivo,
        ]);

        // Actualizar estado del dispositivo
        $this->dispositivo->update([
            'usuario_id' => null,
            'estado' => 'Disponible',
        ]);
    }

    public function confirmarRecepcion()
    {
        $this->update([
            'confirmado' => true,
            'fecha_confirmacion' => now(),
        ]);
    }

    public function getDuracionAsignacionAttribute()
    {
        if (!$this->fecha_asignacion) {
            return 0;
        }

        $fechaInicio = $this->fecha_asignacion instanceof \Carbon\Carbon
            ? $this->fecha_asignacion
            : \Carbon\Carbon::parse($this->fecha_asignacion);

        $fechaFin = $this->fecha_desasignacion
            ? ($this->fecha_desasignacion instanceof \Carbon\Carbon
                ? $this->fecha_desasignacion
                : \Carbon\Carbon::parse($this->fecha_desasignacion))
            : now();

        return $fechaInicio->diffInDays($fechaFin);
    }

    public function getEstaActivaAttribute()
    {
        return is_null($this->fecha_desasignacion);
    }
}
