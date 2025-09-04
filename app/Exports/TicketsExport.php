<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $tickets;

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->tickets;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Descripción',
            'Estado',
            'Prioridad',
            'Tipo',
            'Categorías ITIL',
            'Estado SLA',
            'Tiempo Restante',
            'Área',
            'Creado por',
            'Asignado a',
            'Fecha Creación',
            'Fecha Actualización',
            'Fecha Cierre',
            'Fecha Resolución',
            'Escalado',
            'Fecha Escalamiento',
            'SLA Vencido',
            'Dispositivo',
            'Comentarios Resolución',
            'Archivos Adjuntos',
            'SLA Configurado (Horas)'
        ];
    }

    public function map($ticket): array
    {
        // Obtener categorías ITIL
        $categorias = $ticket->categorias->pluck('nombre')->join(', ');

        // Formatear tiempo restante
        $tiempoRestante = 'N/A';
        if (in_array($ticket->estado, ['Cerrado', 'Cancelado', 'Archivado'])) {
            $fechaResolucion = $ticket->fecha_resolucion ?? $ticket->fecha_cierre;
            if ($fechaResolucion) {
                $tiempoReal = abs($fechaResolucion->diffInMinutes($ticket->created_at));
                $tiempoRestante = "Completado en " . Ticket::formatTiempo($tiempoReal);
            }
        } else {
            $tiempo = $ticket->getTiempoRestanteSla('respuesta');
            if ($tiempo !== null) {
                $tiempoRestante = Ticket::formatTiempo($tiempo);
            }
        }

        // Formatear archivos adjuntos
        $archivos = '';
        if ($ticket->attachment && is_array($ticket->attachment)) {
            $archivos = implode(', ', array_map(function ($file) {
                return basename($file);
            }, $ticket->attachment));
        }

        return [
            $ticket->id,
            $ticket->titulo,
            $ticket->descripcion,
            $ticket->estado,
            $ticket->prioridad,
            $ticket->tipo ?? 'N/A',
            $categorias ?: 'Sin categorías',
            $ticket->getEstadoSla(),
            $tiempoRestante,
            $ticket->area->nombre ?? 'Sin área',
            $ticket->creadoPor->name ?? 'N/A',
            $ticket->asignadoA->name ?? 'Sin asignar',
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->updated_at->format('d/m/Y H:i'),
            $ticket->fecha_cierre ? $ticket->fecha_cierre->format('d/m/Y H:i') : '',
            $ticket->fecha_resolucion ? $ticket->fecha_resolucion->format('d/m/Y H:i') : '',
            $ticket->escalado ? 'Sí' : 'No',
            $ticket->fecha_escalamiento ? $ticket->fecha_escalamiento->format('d/m/Y H:i') : '',
            $ticket->sla_vencido ? 'Sí' : 'No',
            $ticket->dispositivo ? $ticket->dispositivo->nombre : 'Sin dispositivo',
            $ticket->comentarios_resolucion ?? '',
            $archivos,
            $ticket->area && $ticket->area->slas->isNotEmpty() ? $ticket->area->slas->first()->tiempo_respuesta_horas : 'Sin SLA'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],

            // Set background color for header
            'A1:W1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FF4CAF50',
                    ],
                ],
                'font' => [
                    'color' => ['argb' => 'FFFFFFFF'],
                    'bold' => true,
                ],
            ],
        ];
    }
}
