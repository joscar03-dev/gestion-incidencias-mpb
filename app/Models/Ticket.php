<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TicketComment;
use Kirschbaum\Commentions\Contracts\Commentable;
use Kirschbaum\Commentions\HasComments;

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
    ];

    const PRIORIDAD =
    [
        'Baja' => 'Baja',
        'Media' => 'Media',
        'Alta' => 'Alta',
    ];

    const ESTADOS =
    [
        'Abierto' => 'Abierto',
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
    // Relación directa con Area
    public function area()
    {
        // Si el área viene del usuario creador:
        return $this->creadoPor ? $this->creadoPor->area() : null;
    }

    public function sla()
    {
        return $this->belongsTo(Sla::class, 'sla_id');
    }

    protected static function booted()
    {
        static::updating(function ($ticket) {
            if (
                $ticket->isDirty('estado') &&
                $ticket->estado === self::ESTADOS['Cerrado']
            ) {
                $ticket->fecha_cierre = now();
            }
        });
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
}
