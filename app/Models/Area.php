<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'parent_id',
    ];

    // Relación con el área padre
    public function padre()
    {
        return $this->belongsTo(Area::class, 'parent_id');
    }

    // Relación con las áreas hijas
    public function hijas()
    {
        return $this->hasMany(Area::class, 'parent_id');
    }

    // Usuarios asignados a esta área
    public function usuarios()
    {
        return $this->hasMany(User::class, 'area_id');
    }
    // Relación con el área padre (recursiva)
    public function parent()
    {
        return $this->belongsTo(Area::class, 'parent_id');
    }
    // Relación con el local
    public function locale()
    {
        return $this->belongsTo(Locale::class, 'local_id');
    }
    // Relación con los dispositivos en esta área
    public function dispositivos()
    {
        return $this->hasMany(Dispositivo::class, 'area_id');
    }
    // Relación con los niveles de servicio (SLAs) asociados a esta área
    public function slas()
    {
        return $this->hasMany(Sla::class, 'area_id');
    }

    // Relación con el SLA principal (activo) de esta área
    public function sla()
    {
        return $this->hasOne(Sla::class, 'area_id')->where('activo', true);
    }
}
