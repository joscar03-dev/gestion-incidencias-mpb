<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\TicketSatisfaction;
use Carbon\Carbon;

class NPSDashboardWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected ?string $heading = 'NPS Dashboard';

    protected function getStats(): array
    {
        // Obtener datos del período actual (últimos 30 días)
        $currentPeriod = $this->getCurrentPeriodData();
        $previousPeriod = $this->getPreviousPeriodData();

        // Calcular NPS
        $currentNPS = $this->calculateNPS($currentPeriod);
        $previousNPS = $this->calculateNPS($previousPeriod);
        $npsChange = $currentNPS - $previousNPS;

        // Calcular distribución
        $promoters = $currentPeriod->whereIn('rating', [4, 5])->count();
        $detractors = $currentPeriod->whereIn('rating', [1, 2])->count();
        $neutros = $currentPeriod->where('rating', 3)->count();
        $total = $currentPeriod->count();

        // Satisfacción promedio
        $avgRating = $currentPeriod->avg('rating') ?: 0;
        $previousAvgRating = $previousPeriod->avg('rating') ?: 0;
        $ratingChange = $avgRating - $previousAvgRating;

        return [
            // 1. NPS Score Principal
            Stat::make('NPS Score', $this->formatNPS($currentNPS))
                ->description($this->getNPSDescription($currentNPS, $npsChange))
                ->descriptionIcon($npsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($this->getNPSColor($currentNPS))
                ->chart($this->getNPSChart()),

            // 2. Promotores
            Stat::make('🟢 Promotores', $promoters)
                ->description($total > 0 ? round(($promoters / $total) * 100, 1) . '% del total' : '0%')
                ->descriptionIcon('heroicon-m-face-smile')
                ->color('success'),

            // 3. Detractores  
            Stat::make('🔴 Detractores', $detractors)
                ->description($total > 0 ? round(($detractors / $total) * 100, 1) . '% del total' : '0%')
                ->descriptionIcon('heroicon-m-face-frown')
                ->color($detractors > ($total * 0.15) ? 'danger' : 'warning'),

            // 4. Satisfacción Promedio
            Stat::make('Satisfacción Promedio', number_format($avgRating, 1) . '/5 ⭐')
                ->description($this->getRatingDescription($ratingChange))
                ->descriptionIcon($ratingChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($this->getRatingColor($avgRating)),

            // 5. Total Respuestas
            Stat::make('Total Respuestas', $total)
                ->description('Últimos 30 días')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),

            // 6. Neutros
            Stat::make('🟡 Neutros', $neutros)
                ->description($total > 0 ? round(($neutros / $total) * 100, 1) . '% del total' : '0%')
                ->descriptionIcon('heroicon-m-face-smile')
                ->color('warning'),
        ];
    }

    private function getCurrentPeriodData()
    {
        return TicketSatisfaction::where('created_at', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('rating')
            ->get();
    }

    private function getPreviousPeriodData()
    {
        return TicketSatisfaction::whereBetween('created_at', [
            Carbon::now()->subDays(60),
            Carbon::now()->subDays(30)
        ])
            ->whereNotNull('rating')
            ->get();
    }

    private function calculateNPS($data)
    {
        $total = $data->count();
        if ($total === 0) return 0;

        $promoters = $data->whereIn('rating', [4, 5])->count();
        $detractors = $data->whereIn('rating', [1, 2])->count();

        $promoterPercentage = ($promoters / $total) * 100;
        $detractorPercentage = ($detractors / $total) * 100;

        return round($promoterPercentage - $detractorPercentage);
    }

    private function formatNPS($nps)
    {
        return ($nps > 0 ? '+' : '') . $nps;
    }

    private function getNPSDescription($nps, $change)
    {
        $trend = '';
        if ($change > 0) {
            $trend = "↗️ +" . abs($change) . " pts vs anterior";
        } elseif ($change < 0) {
            $trend = "↘️ -" . abs($change) . " pts vs anterior";
        } else {
            $trend = "➡️ Sin cambios vs anterior";
        }

        $level = match (true) {
            $nps >= 70 => 'Excelente',
            $nps >= 50 => 'Muy bueno',
            $nps >= 30 => 'Bueno',
            $nps >= 0 => 'Regular',
            default => 'Crítico'
        };

        return "$level - $trend";
    }

    private function getNPSColor($nps)
    {
        return match (true) {
            $nps >= 50 => 'success',
            $nps >= 30 => 'warning',
            $nps >= 0 => 'info',
            default => 'danger'
        };
    }

    private function getNPSChart()
    {
        // Obtener datos de los últimos 7 días para el mini gráfico
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayData = TicketSatisfaction::whereDate('created_at', $date)
                ->whereNotNull('rating')
                ->get();

            $chartData[] = $this->calculateNPS($dayData);
        }

        return $chartData;
    }

    private function getRatingDescription($change)
    {
        if ($change > 0) {
            return "↗️ +" . number_format($change, 2) . " vs anterior";
        } elseif ($change < 0) {
            return "↘️ " . number_format($change, 2) . " vs anterior";
        }
        return "➡️ Sin cambios vs anterior";
    }

    private function getRatingColor($rating)
    {
        return match (true) {
            $rating >= 4.5 => 'success',
            $rating >= 4.0 => 'info',
            $rating >= 3.5 => 'warning',
            default => 'danger'
        };
    }
}
