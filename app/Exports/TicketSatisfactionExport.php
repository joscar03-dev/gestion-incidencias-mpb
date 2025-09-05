<?php

namespace App\Exports;

use App\Models\TicketSatisfaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketSatisfactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $satisfactions;

    public function __construct($satisfactions)
    {
        $this->satisfactions = $satisfactions;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->satisfactions->load(['ticket.asignadoA', 'user']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'ID Ticket',
            'Título Ticket',
            'Usuario',
            'Email Usuario',
            'Técnico Asignado',
            'Email Técnico',
            'Calificación (1-5)',
            'Calificación Texto',
            'Tiempo Respuesta',
            'Tiempo Texto',
            'Categoría NPS',
            'Comentarios',
            'Tipo Ticket',
            'Prioridad',
            'Estado Ticket',
            'Ticket Creado',
            'Ticket Cerrado',
            'Tiempo Resolución (hrs)',
            'Fecha Encuesta',
            'Registrado',
            'Días para Responder',
            'Trimestre',
            'Mes/Año'
        ];
    }

    public function map($satisfaction): array
    {
        // Textos para rating
        $ratingText = match ($satisfaction->rating) {
            1 => 'Muy malo',
            2 => 'Malo',
            3 => 'Regular',
            4 => 'Bueno',
            5 => 'Excelente',
            default => 'Sin calificar'
        };

        // Textos para tiempo
        $timeText = match ($satisfaction->time_satisfaction) {
            'muy_rapido' => 'Muy rápido',
            'adecuado' => 'Adecuado',
            'regular' => 'Regular',
            'muy_lento' => 'Muy lento',
            default => 'Sin calificar'
        };

        // Categoría NPS
        $npsCategory = match ($satisfaction->rating) {
            4, 5 => 'Promotor',
            3 => 'Neutro',
            1, 2 => 'Detractor',
            default => 'Sin categorizar'
        };

        // Días para responder
        $daysToRespond = '';
        if ($satisfaction->ticket && $satisfaction->submitted_at && $satisfaction->ticket->fecha_cierre) {
            $daysToRespond = $satisfaction->submitted_at->diffInDays($satisfaction->ticket->fecha_cierre);
        }

        // Trimestre
        $quarter = '';
        if ($satisfaction->submitted_at) {
            $q = ceil($satisfaction->submitted_at->month / 3);
            $quarter = 'Q' . $q . ' ' . $satisfaction->submitted_at->year;
        }

        return [
            $satisfaction->id,
            $satisfaction->ticket->id ?? '',
            $satisfaction->ticket->titulo ?? '',
            $satisfaction->user->name ?? '',
            $satisfaction->user->email ?? '',
            $satisfaction->ticket->asignadoA->name ?? 'Sin asignar',
            $satisfaction->ticket->asignadoA->email ?? '',
            $satisfaction->rating,
            $ratingText,
            $satisfaction->time_satisfaction,
            $timeText,
            $npsCategory,
            $satisfaction->comments ?? '',
            $satisfaction->ticket->tipo ?? '',
            $satisfaction->ticket->prioridad ?? '',
            $satisfaction->ticket->estado ?? '',
            $satisfaction->ticket->created_at ? $satisfaction->ticket->created_at->format('d/m/Y H:i') : '',
            $satisfaction->ticket->fecha_cierre ? $satisfaction->ticket->fecha_cierre->format('d/m/Y H:i') : '',
            $satisfaction->ticket->tiempo_resolucion ?? '',
            $satisfaction->submitted_at ? $satisfaction->submitted_at->format('d/m/Y H:i') : '',
            $satisfaction->created_at->format('d/m/Y H:i'),
            $daysToRespond,
            $quarter,
            $satisfaction->submitted_at ? $satisfaction->submitted_at->format('m/Y') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],

            // Set background color for header
            'A1:X1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FF2196F3', // Azul para encuestas
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
