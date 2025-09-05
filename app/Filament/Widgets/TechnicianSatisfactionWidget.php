<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketSatisfaction;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Support\Colors\Color;
use Illuminate\Support\Str;

class TechnicianSatisfactionWidget extends BaseWidget
{
    protected static ?string $heading = '👥 Ranking de Satisfacción por Técnico';
    protected static ?string $description = 'Evaluación de desempeño para decisiones de personal (últimos 30 días)';
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->columns([
                TextColumn::make('ranking')
                    ->label('🏆 Rank')
                    ->getStateUsing(function ($record, $rowLoop) {
                        $position = $rowLoop->iteration;
                        return match (true) {
                            $position === 1 => '🥇 #1',
                            $position === 2 => '🥈 #2',
                            $position === 3 => '🥉 #3',
                            default => "#{$position}"
                        };
                    })
                    ->width('80px')
                    ->alignCenter(),

                TextColumn::make('name')
                    ->label('👨‍💻 Técnico')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->getStateUsing(fn($record) => $record->name)
                    ->description(fn($record) => $record->email),

                TextColumn::make('avg_rating')
                    ->label('⭐ Rating')
                    ->getStateUsing(function ($record) {
                        $avgRating = $this->getTechnicianStats($record->id)['avg_rating'];
                        return number_format($avgRating, 1) . '/5';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $avgRating = $this->getTechnicianStats($record->id)['avg_rating'];
                        return match (true) {
                            $avgRating >= 4.5 => 'success',
                            $avgRating >= 4.0 => 'info',
                            $avgRating >= 3.5 => 'warning',
                            default => 'danger'
                        };
                    })
                    ->alignCenter(),

                TextColumn::make('total_tickets')
                    ->label('📊 Tickets')
                    ->getStateUsing(fn($record) => $this->getTechnicianStats($record->id)['total_tickets'])
                    ->description('evaluados')
                    ->alignCenter(),

                TextColumn::make('nps_score')
                    ->label('🎯 NPS')
                    ->getStateUsing(function ($record) {
                        $nps = $this->getTechnicianStats($record->id)['nps'];
                        return ($nps > 0 ? '+' : '') . $nps;
                    })
                    ->badge()
                    ->color(function ($record) {
                        $nps = $this->getTechnicianStats($record->id)['nps'];
                        return match (true) {
                            $nps >= 50 => 'success',
                            $nps >= 30 => 'info',
                            $nps >= 0 => 'warning',
                            default => 'danger'
                        };
                    })
                    ->alignCenter(),

                TextColumn::make('performance_status')
                    ->label('🚦 Estado')
                    ->getStateUsing(function ($record) {
                        $avgRating = $this->getTechnicianStats($record->id)['avg_rating'];
                        return match (true) {
                            $avgRating >= 4.5 => '🏆 TOP PERFORMER',
                            $avgRating >= 4.0 => '✅ EXCELENTE',
                            $avgRating >= 3.5 => '🟡 BUENO',
                            $avgRating >= 3.0 => '⚠️ REGULAR',
                            default => '🚨 CRÍTICO'
                        };
                    })
                    ->badge()
                    ->color(function ($record) {
                        $avgRating = $this->getTechnicianStats($record->id)['avg_rating'];
                        return match (true) {
                            $avgRating >= 4.0 => 'success',
                            $avgRating >= 3.5 => 'warning',
                            default => 'danger'
                        };
                    }),

                TextColumn::make('decision_action')
                    ->label('💼 Acción Recomendada')
                    ->getStateUsing(function ($record) {
                        $stats = $this->getTechnicianStats($record->id);
                        $avgRating = $stats['avg_rating'];
                        $totalTickets = $stats['total_tickets'];

                        if ($totalTickets < 5) {
                            return '📊 Datos insuficientes';
                        }

                        return match (true) {
                            $avgRating >= 4.5 => '🎁 Bonificar/Reconocer',
                            $avgRating >= 4.0 => '📈 Mantener nivel',
                            $avgRating >= 3.5 => '📚 Capacitación ligera',
                            $avgRating >= 3.0 => '🔧 Plan de mejora',
                            default => '🚨 Reasignar/Capacitar urgente'
                        };
                    })
                    ->color(function ($record) {
                        $avgRating = $this->getTechnicianStats($record->id)['avg_rating'];
                        return match (true) {
                            $avgRating >= 4.0 => 'success',
                            $avgRating >= 3.5 => 'warning',
                            default => 'danger'
                        };
                    }),

                TextColumn::make('last_review')
                    ->label('💬 Último Comentario')
                    ->getStateUsing(function ($record) {
                        $lastComment = TicketSatisfaction::whereHas('ticket', function ($query) use ($record) {
                            $query->where('asignado_a', $record->id);
                        })
                            ->where('created_at', '>=', Carbon::now()->subDays(30))
                            ->whereNotNull('comments')
                            ->where('comments', '!=', '')
                            ->latest()
                            ->first();

                        return $lastComment ?
                            '"' . Str::limit($lastComment->comments, 40) . '"' :
                            'Sin comentarios recientes';
                    })
                    ->limit(50)
                    ->tooltip(function ($record) {
                        $lastComment = TicketSatisfaction::whereHas('ticket', function ($query) use ($record) {
                            $query->where('asignado_a', $record->id);
                        })
                            ->where('created_at', '>=', Carbon::now()->subDays(30))
                            ->whereNotNull('comments')
                            ->where('comments', '!=', '')
                            ->latest()
                            ->first();

                        return $lastComment ? $lastComment->comments : null;
                    }),
            ])
            ->defaultSort('name', 'asc')
            ->paginated(false);
    }

    private function getQuery(): Builder
    {
        // Obtener técnicos que tienen tickets asignados con encuestas en los últimos 30 días
        return User::query()
            ->whereHas('ticketsAsignados.satisfaction', function ($query) {
                $query->where('ticket_satisfactions.created_at', '>=', Carbon::now()->subDays(30))
                    ->whereNotNull('rating');
            })
            ->with(['ticketsAsignados.satisfaction' => function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->whereNotNull('rating');
            }])
            ->orderBy('name');
    }
    private function getTechnicianStats($technicianId): array
    {
        // Obtener todas las satisfacciones del técnico en los últimos 30 días
        $satisfactions = TicketSatisfaction::whereHas('ticket', function ($query) use ($technicianId) {
            $query->where('asignado_a', $technicianId);
        })
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('rating')
            ->get();

        $totalTickets = $satisfactions->count();

        if ($totalTickets === 0) {
            return [
                'avg_rating' => 0,
                'total_tickets' => 0,
                'nps' => 0,
                'promoters' => 0,
                'detractors' => 0,
                'neutros' => 0,
            ];
        }

        $avgRating = $satisfactions->avg('rating');
        $promoters = $satisfactions->whereIn('rating', [4, 5])->count();
        $detractors = $satisfactions->whereIn('rating', [1, 2])->count();
        $neutros = $satisfactions->where('rating', 3)->count();

        // Calcular NPS
        $promoterPercentage = ($promoters / $totalTickets) * 100;
        $detractorPercentage = ($detractors / $totalTickets) * 100;
        $nps = round($promoterPercentage - $detractorPercentage);

        return [
            'avg_rating' => round($avgRating, 2),
            'total_tickets' => $totalTickets,
            'nps' => $nps,
            'promoters' => $promoters,
            'detractors' => $detractors,
            'neutros' => $neutros,
        ];
    }
}
