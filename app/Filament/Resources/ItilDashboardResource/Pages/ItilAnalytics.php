<?php

namespace App\Filament\Resources\ItilDashboardResource\Pages;

use App\Filament\Resources\ItilDashboardResource;
use App\Models\ItilDashboard;
use Filament\Resources\Pages\Page;
use Filament\Pages\Actions\Action;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ItilAnalytics extends Page
{
    protected static string $resource = ItilDashboardResource::class;

    protected static string $view = 'filament.resources.itil-dashboard-resource.pages.itil-analytics';

    protected static ?string $title = 'Analytics ITIL - Indicadores de Eficiencia y Calidad';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Exportar a PDF')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->exportToPdf();
                }),
            Action::make('refresh')
                ->label('Actualizar Analytics')
                ->icon('heroicon-m-arrow-path')
                ->action(function () {
                    $this->redirect(request()->header('Referer'));
                }),
        ];
    }

    public function getViewData(): array
    {
        return [
            'efficiency_indicators' => ItilDashboard::getEfficiencyIndicators(),
            'quality_indicators' => ItilDashboard::getQualityIndicators(),
            'performance_comparison' => ItilDashboard::getPerformanceComparison('month'),
            'period' => 'month',
            'last_updated' => now()->format('d/m/Y H:i:s')
        ];
    }

    public function exportToPdf()
    {
        try {
            // Obtener los datos
            $data = $this->getViewData();
            
            // Generar el PDF
            $pdf = Pdf::loadView('exports.itil-analytics-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('defaultFont', 'sans-serif')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true);

            $filename = 'itil-analytics-' . now()->format('Y-m-d-H-i-s') . '.pdf';

            Notification::make()
                ->title('Reporte de Analytics Exportado')
                ->body('El reporte de indicadores ITIL se ha generado correctamente.')
                ->success()
                ->send();

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al Exportar')
                ->body('Ocurrió un error al generar el reporte: ' . $e->getMessage())
                ->danger()
                ->send();
                
            return null;
        }
    }
}
