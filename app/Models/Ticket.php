<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'descripcion', 'estado', 'prioridad', 'comentario', 'asignado_a', 'asignado_por', 'is_resolved', 'attachment'];

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
}
