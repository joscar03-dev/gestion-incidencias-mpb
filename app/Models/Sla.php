<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
