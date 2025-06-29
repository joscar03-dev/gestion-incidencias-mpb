<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'direccion'];

    public function areas()
    {
        return $this->hasMany(Area::class, 'local_id');
    }
}
