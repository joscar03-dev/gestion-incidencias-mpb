<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Illuminate\Support\Carbon;
use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\LineChartWidget;

class TicketsTiempoResolucionChart extends ChartWidget
{
    protected static ?string $heading = 'Comparativo: Tiempo Real vs SLA por Ticket';
    protected int | string | array $columnSpan = 'full';
    public ?string $filter = 'week';

    public function getDescription(): ?string
    {
        $periodo = match ($this->filter) {
            'today' => 'hoy',
            'week' => 'la última semana',
            'month' => 'el último mes',
            'quarter' => 'los últimos 3 meses',
            'year' => 'el último año',
            'all' => 'todos los tiempos',
            default => 'la última semana'
        };

        return "Últimos 15 tickets cerrados en {$periodo}. Cada ticket muestra: 🔵 SLA Configurado | 🟢 Verde: cumplió SLA | 🔴 Rojo: excedió SLA. Tiempos en formato legible (ej: 2h 30m).";
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hoy',
            'week' => 'Última semana',
            'month' => 'Último mes',
            'quarter' => 'Últimos 3 meses',
            'year' => 'Último año',
            'all' => 'Todos los tiempos',
        ];
    }
    /**
     * Formatear minutos en formato legible
     */
    private function formatearTiempo($minutos)
    {
        if ($minutos < 60) {
            return $minutos . 'm';
        } elseif ($minutos < 1440) { // Menos de 24 horas
            $horas = floor($minutos / 60);
            $mins = $minutos % 60;
            return $mins > 0 ? $horas . 'h ' . $mins . 'm' : $horas . 'h';
        } else { // 24 horas o más
            $dias = floor($minutos / 1440);
            $horasRestantes = floor(($minutos % 1440) / 60);
            $minsRestantes = $minutos % 60;

            $resultado = $dias . 'd';
            if ($horasRestantes > 0) $resultado .= ' ' . $horasRestantes . 'h';
            if ($minsRestantes > 0) $resultado .= ' ' . $minsRestantes . 'm';

            return $resultado;
        }
    }

    /**
     * Obtener rango de fechas según filtro
     */
    private function obtenerRangoFechas($filtro)
    {
        return match ($filtro) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->subWeek()->startOfDay(), now()->endOfDay()],
            'month' => [now()->subMonth()->startOfDay(), now()->endOfDay()],
            'quarter' => [now()->subMonths(3)->startOfDay(), now()->endOfDay()],
            'year' => [now()->subYear()->startOfDay(), now()->endOfDay()],
            'all' => [now()->subYears(2)->startOfDay(), now()->endOfDay()],
            default => [now()->subWeek()->startOfDay(), now()->endOfDay()],
        };
    }

    protected function getData(): array
    {
        // Obtener rango de fechas según filtro
        [$inicio, $fin] = $this->obtenerRangoFechas($this->filter);

        // Construir la consulta base con relaciones necesarias
        $query = Ticket::where('estado', Ticket::ESTADOS['Cerrado'])
            ->whereBetween('fecha_cierre', [$inicio, $fin])
            ->with(['area.slas', 'creadoPor', 'asignadoA', 'categorias']); // Eager loading para mejor performance

        $tickets = $query->orderBy('fecha_cierre', 'desc')->take(15)->get(); // Últimos 15 tickets

        $labels = [];
        $tiemposSla = []; // Barras azules - SLA configurado
        $tiemposReales = []; // Barras verdes/rojas - Tiempo real
        $coloresTiempoReal = []; // Colores dinámicos para tiempo real
        $ticketsData = []; // Datos completos de tickets para tooltips

        foreach ($tickets as $ticket) {
            $fechaCierre = $ticket->fecha_cierre ? Carbon::parse($ticket->fecha_cierre) : Carbon::parse($ticket->updated_at);
            $minutosReales = Carbon::parse($ticket->created_at)->diffInMinutes($fechaCierre);

            // Obtener SLA efectivo del ticket
            $slaEfectivo = $ticket->getSlaEfectivo();
            $tiempoSlaMinutos = 480; // Default 8 horas

            if ($slaEfectivo && isset($slaEfectivo['tiempo_resolucion'])) {
                $tiempoSlaMinutos = $slaEfectivo['tiempo_resolucion'];
            }

            $vencido = $minutosReales > $tiempoSlaMinutos;

            // Crear etiquetas más informativas con tiempo formateado
            $tituloCorto = strlen($ticket->titulo) > 15 ? substr($ticket->titulo, 0, 15) . '...' : $ticket->titulo;
            $tiempoRealFormateado = $this->formatearTiempo($minutosReales);
            $tiempoSlaFormateado = $this->formatearTiempo($tiempoSlaMinutos);

            $labels[] = "#{$ticket->id} - {$tituloCorto}";

            // Guardar datos completos del ticket para tooltips
            $ticketsData[] = [
                'id' => $ticket->id,
                'titulo' => $ticket->titulo,
                'descripcion' => $ticket->descripcion,
                'creado_por' => $ticket->creadoPor ? $ticket->creadoPor->name : 'No asignado',
                'asignado_a' => $ticket->asignadoA ? $ticket->asignadoA->name : 'No asignado',
                'prioridad' => $ticket->prioridad ?: 'Sin prioridad',
                'categoria' => $ticket->categorias->first() ? $ticket->categorias->first()->nombre : 'Sin categoría',
                'created_at' => Carbon::parse($ticket->created_at)->format('d/m/Y H:i'),
                'fecha_cierre' => $fechaCierre->format('d/m/Y H:i'),
                'tiempo_real_formateado' => $tiempoRealFormateado,
                'tiempo_sla_formateado' => $tiempoSlaFormateado,
                'tiempo_real_minutos' => $minutosReales,
                'tiempo_sla_minutos' => $tiempoSlaMinutos,
                'cumple_sla' => !$vencido,
                'estado' => 'Cerrado'
            ];

            // Usar minutos como base para el gráfico (convertir a valores que Chart.js pueda manejar)
            // Pero mostrar en formato legible en tooltips
            $tiemposSla[] = $tiempoSlaMinutos; // En minutos para cálculos
            $tiemposReales[] = $minutosReales; // En minutos para cálculos

            // Color según cumplimiento SLA
            $coloresTiempoReal[] = $vencido ? '#ef4444' : '#10b981'; // Rojo si excede, verde si cumple
        }

        return [
            'datasets' => [
                [
                    'label' => '🔵 SLA Configurado',
                    'data' => $tiemposSla,
                    'backgroundColor' => '#3b82f6', // Azul constante
                    'borderColor' => '#2563eb',
                    'borderWidth' => 1,
                    'barThickness' => 20,
                ],
                [
                    'label' => '⏱️ Tiempo Real de Resolución',
                    'data' => $tiemposReales,
                    'backgroundColor' => $coloresTiempoReal, // Verde o rojo según cumplimiento
                    'borderColor' => $coloresTiempoReal,
                    'borderWidth' => 1,
                    'barThickness' => 20,
                ],
            ],
            'labels' => $labels,
            'ticketsData' => $ticketsData, // Pasar datos completos para tooltips
        ];
    }
    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => '#ffffff',
                    'bodyColor' => '#ffffff',
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 1,
                    'cornerRadius' => 6,
                    'displayColors' => true,
                    'callbacks' => [
                        'title' => "function(context) {
                            if (context.length > 0) {
                                const dataIndex = context[0].dataIndex;
                                const ticketData = context[0].chart.data.ticketsData[dataIndex];
                                return '🎫 Ticket #' + ticketData.id + ' - ' + ticketData.titulo;
                            }
                            return '';
                        }",
                        'label' => "function(context) {
                            const dataIndex = context.dataIndex;
                            const ticketData = context.chart.data.ticketsData[dataIndex];
                            const label = context.dataset.label || '';
                            const valueInMinutes = context.parsed.y;
                            
                            // Función para formatear minutos en JavaScript
                            function formatTime(minutes) {
                                if (minutes < 60) {
                                    return minutes + 'm';
                                } else if (minutes < 1440) {
                                    const hours = Math.floor(minutes / 60);
                                    const mins = minutes % 60;
                                    return mins > 0 ? hours + 'h ' + mins + 'm' : hours + 'h';
                                } else {
                                    const days = Math.floor(minutes / 1440);
                                    const remainingHours = Math.floor((minutes % 1440) / 60);
                                    const remainingMins = minutes % 60;
                                    
                                    let result = days + 'd';
                                    if (remainingHours > 0) result += ' ' + remainingHours + 'h';
                                    if (remainingMins > 0) result += ' ' + remainingMins + 'm';
                                    
                                    return result;
                                }
                            }
                            
                            return label + ': ' + formatTime(valueInMinutes);
                        }",
                        'afterLabel' => "function(context) {
                            const dataIndex = context.dataIndex;
                            const ticketData = context.chart.data.ticketsData[dataIndex];
                            
                            const lines = [
                                '👤 Creado por: ' + ticketData.creado_por,
                                '👨‍💻 Asignado a: ' + ticketData.asignado_a,
                                '🎯 Prioridad: ' + ticketData.prioridad,
                                '📂 Categoría: ' + ticketData.categoria,
                                '📅 Creado: ' + ticketData.created_at,
                                '✅ Cerrado: ' + ticketData.fecha_cierre,
                                '📊 Estado SLA: ' + (ticketData.cumple_sla ? '✅ Cumplido' : '❌ Excedido')
                            ];
                            
                            return lines;
                        }"
                    ]
                ]
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Tickets (más recientes primero)',
                        'font' => [
                            'size' => 12,
                            'weight' => 'bold'
                        ]
                    ],
                    'ticks' => [
                        'display' => true,
                        'maxRotation' => 45,
                        'minRotation' => 0,
                        'font' => [
                            'size' => 10
                        ],
                        'color' => '#6b7280'
                    ],
                    'grid' => [
                        'display' => true,
                        'color' => '#e5e7eb'
                    ]
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Tiempo de Resolución',
                        'font' => [
                            'size' => 12,
                            'weight' => 'bold'
                        ]
                    ],
                    'beginAtZero' => true,
                    'ticks' => [
                        'display' => true,
                        'font' => [
                            'size' => 10
                        ],
                        'color' => '#6b7280',
                        'callback' => "function(value) {
                            // Formatear etiquetas del eje Y
                            if (value < 60) {
                                return value + 'm';
                            } else if (value < 1440) {
                                const hours = Math.floor(value / 60);
                                const mins = value % 60;
                                return mins > 0 ? hours + 'h ' + mins + 'm' : hours + 'h';
                            } else {
                                const days = Math.floor(value / 1440);
                                const remainingHours = Math.floor((value % 1440) / 60);
                                let result = days + 'd';
                                if (remainingHours > 0) result += ' ' + remainingHours + 'h';
                                return result;
                            }
                        }"
                    ],
                    'grid' => [
                        'display' => true,
                        'color' => '#e5e7eb'
                    ]
                ]
            ],
            'elements' => [
                'bar' => [
                    'borderWidth' => 1,
                ]
            ],
            'barPercentage' => 0.8,
            'categoryPercentage' => 0.9,
        ];
    }
}
