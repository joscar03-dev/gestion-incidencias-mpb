<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'numero_serie',
        'estado',
        'area_id',
        'usuario_id',
        'fecha_compra',
    ];

    public function categoria_dispositivo()
    {
        return $this->belongsTo(CategoriaDispositivo::class, 'categoria_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function asignaciones()
    {
        return $this->hasMany(\App\Models\DispositivoAsignacion::class);
    }
}
