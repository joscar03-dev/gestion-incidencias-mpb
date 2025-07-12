<?php

namespace App\Console\Commands;

use App\Models\ItilDashboard;
use App\Exports\ItilReportExport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class GenerateItilReport extends Command
{
    protected $signature = 'itil:report 
                            {--type=general : Tipo de reporte (general, sla, metricas, tendencias)}
                            {--format=excel : Formato de salida (excel, pdf)}
                            {--from= : Fecha desde (Y-m-d)}
                            {--to= : Fecha hasta (Y-m-d)}
                            {--output= : Archivo de salida}';

    protected $description = 'Genera reportes ITIL automáticos';

    public function handle()
    {
        $this->info('🚀 Iniciando generación de reporte ITIL...');

        $type = $this->option('type');
        $format = $this->option('format');
        $from = $this->option('from');
        $to = $this->option('to');
        $output = $this->option('output');

        // Validar fechas
        if ($from && !Carbon::createFromFormat('Y-m-d', $from)) {
            $this->error('Formato de fecha "from" inválido. Use Y-m-d');
            return 1;
        }

        if ($to && !Carbon::createFromFormat('Y-m-d', $to)) {
            $this->error('Formato de fecha "to" inválido. Use Y-m-d');
            return 1;
        }

        // Configurar parámetros del reporte
        $filters = [
            'tipo_reporte' => $type,
            'fecha_desde' => $from,
            'fecha_hasta' => $to,
        ];

        // Mostrar información del reporte
        $this->table(
            ['Parámetro', 'Valor'],
            [
                ['Tipo de Reporte', ucfirst($type)],
                ['Formato', strtoupper($format)],
                ['Fecha Desde', $from ?: 'No especificada'],
                ['Fecha Hasta', $to ?: 'No especificada'],
                ['Archivo Salida', $output ?: 'Automático'],
            ]
        );

        if (!$this->confirm('¿Continuar con la generación del reporte?')) {
            $this->info('Operación cancelada.');
            return 0;
        }

        $this->info('📊 Recopilando métricas ITIL...');

        // Generar métricas
        $bar = $this->output->createProgressBar(5);
        
        $bar->start();
        $metrics = ItilDashboard::getIncidentMetrics();
        $bar->advance();
        
        $resolutionMetrics = ItilDashboard::getResolutionTimeMetrics();
        $bar->advance();
        
        $categoryDistribution = ItilDashboard::getCategoryDistribution();
        $bar->advance();
        
        $serviceAvailability = ItilDashboard::getServiceAvailabilityMetrics();
        $bar->advance();
        
        $workloadAnalysis = ItilDashboard::getWorkloadAnalysis();
        $bar->finish();
        
        $this->newLine(2);

        // Mostrar resumen de métricas
        $this->info('📈 Resumen de Métricas:');
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total Incidentes', $metrics['total_incidents']],
                ['Tasa Resolución', $metrics['resolution_rate'] . '%'],
                ['Cumplimiento SLA', $metrics['sla_compliance'] . '%'],
                ['Disponibilidad', $serviceAvailability['availability_percentage'] . '%'],
                ['MTTR', round($resolutionMetrics['mean_time_to_resolve'] ?? 0, 2) . ' horas'],
                ['Analistas Activos', count($workloadAnalysis)],
            ]
        );

        // Generar archivo de salida
        if (!$output) {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $output = "reporte-itil-{$type}-{$timestamp}.{$format}";
        }

        $this->info("📄 Generando archivo: {$output}");

        try {
            if ($format === 'excel') {
                Excel::store(new ItilReportExport($filters), $output, 'public');
                $this->info("✅ Reporte Excel generado exitosamente en: storage/app/public/{$output}");
            } else {
                $this->warn("⚠️  Formato PDF en desarrollo. Generando Excel por defecto.");
                Excel::store(new ItilReportExport($filters), str_replace('.pdf', '.xlsx', $output), 'public');
                $this->info("✅ Reporte Excel generado exitosamente en: storage/app/public/" . str_replace('.pdf', '.xlsx', $output));
            }

            // Mostrar estadísticas adicionales
            $this->newLine();
            $this->info('📊 Estadísticas Adicionales:');
            $this->line("• Incidentes escalados: {$metrics['escalated_incidents']}");
            $this->line("• SLA incumplidos: {$metrics['sla_breached']}");
            $this->line("• Tasa de escalamiento: {$metrics['escalation_rate']}%");
            
            $this->newLine();
            $this->info('🎯 Recomendaciones:');
            
            if ($metrics['sla_compliance'] < 90) {
                $this->warn("• Mejorar cumplimiento SLA (actual: {$metrics['sla_compliance']}%)");
            }
            
            if ($metrics['escalation_rate'] > 10) {
                $this->warn("• Reducir tasa de escalamiento (actual: {$metrics['escalation_rate']}%)");
            }
            
            if ($serviceAvailability['availability_percentage'] < 99) {
                $this->warn("• Mejorar disponibilidad del servicio (actual: {$serviceAvailability['availability_percentage']}%)");
            }
            
            if (count($workloadAnalysis) > 0) {
                $overloaded = collect($workloadAnalysis)->where('open_tickets', '>', 10)->count();
                if ($overloaded > 0) {
                    $this->warn("• {$overloaded} analistas con sobrecarga de trabajo");
                }
            }

            $this->newLine();
            $this->info('✨ Reporte ITIL generado exitosamente!');
            
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error al generar el reporte: " . $e->getMessage());
            return 1;
        }
    }
}
