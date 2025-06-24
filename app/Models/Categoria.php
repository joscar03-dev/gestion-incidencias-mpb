<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'slug', 'is_active'];

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class);
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
