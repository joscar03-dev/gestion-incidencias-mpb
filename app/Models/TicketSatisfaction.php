<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketSatisfaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'rating',
        'time_satisfaction',
        'comments',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // Relaciones
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Métodos auxiliares
    public function getRatingTextAttribute()
    {
        return match ($this->rating) {
            1 => '😡 Muy malo',
            2 => '😞 Malo',
            3 => '😐 Regular',
            4 => '😊 Bueno',
            5 => '😄 Excelente',
            default => 'Sin calificar'
        };
    }

    public function getTimeSatisfactionTextAttribute()
    {
        return match ($this->time_satisfaction) {
            'muy_rapido' => '⚡ Muy rápido',
            'adecuado' => '✅ Adecuado',
            'regular' => '🕐 Regular',
            'muy_lento' => '🐌 Muy lento',
            default => 'Sin calificar'
        };
    }

    // Scopes
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopePositive($query)
    {
        return $query->whereIn('rating', [4, 5]);
    }

    public function scopeNegative($query)
    {
        return $query->whereIn('rating', [1, 2]);
    }
}
