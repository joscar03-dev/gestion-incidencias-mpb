<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispositivoAsignacion extends Model
{
    protected $fillable = [
        'dispositivo_id',
        'user_id',
        'fecha_asignacion',
        'fecha_desasignacion',
    ];

    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
