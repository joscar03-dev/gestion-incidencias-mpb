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
            'Área',
            'Creado por',
            'Asignado a',
            'Fecha Creación',
            'Fecha Actualización',
            'Escalado',
            'SLA Vencido',
            'Comentarios'
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->id,
            $ticket->titulo,
            $ticket->descripcion,
            $ticket->estado,
            $ticket->prioridad,
            $ticket->area->nombre ?? 'Sin área',
            $ticket->creadoPor->name ?? 'N/A',
            $ticket->asignadoA->name ?? 'Sin asignar',
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->updated_at->format('d/m/Y H:i'),
            $ticket->escalado ? 'Sí' : 'No',
            $ticket->sla_vencido ? 'Sí' : 'No',
            $ticket->comentario ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
            
            // Set background color for header
            'A1:M1' => [
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
