<?php

namespace App\Console\Commands;

use App\Models\ItilDashboard;
use Illuminate\Console\Command;

class TestWorkloadAnalysis extends Command
{
    protected $signature = 'test:workload';
    protected $description = 'Test workload analysis data';

    public function handle()
    {
        $this->info('Probando análisis de carga de trabajo...');
        
        $workload = ItilDashboard::getWorkloadAnalysis();
        
        $this->table(
            ['Técnico', 'Abiertos', 'Resueltos', 'Escalados', 'Total', 'Tasa Resolución', 'Tasa Escalación'],
            collect($workload)->map(function ($analyst) {
                return [
                    $analyst['user_name'],
                    $analyst['open_tickets'],
                    $analyst['resolved_tickets'],
                    $analyst['escalated_tickets'],
                    $analyst['total_tickets'],
                    $analyst['resolution_rate'] . '%',
                    $analyst['escalation_rate'] . '%',
                ];
            })
        );
        
        $this->info('Total de técnicos con tickets asignados: ' . count($workload));
        
        return Command::SUCCESS;
    }
}
