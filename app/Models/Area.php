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
    public function local()
    {
        return $this->belongsTo(Locale::class, 'local_id');
    }
}
