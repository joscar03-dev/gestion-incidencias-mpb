<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaDispositivo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    public function dispositivos()
    {
        return $this->hasMany(Dispositivo::class, 'categoria_id');
    }
}
