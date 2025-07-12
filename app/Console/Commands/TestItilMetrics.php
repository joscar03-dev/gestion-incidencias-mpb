<?php

namespace App\Console\Commands;

use App\Models\ItilDashboard;
use App\Models\Ticket;
use Illuminate\Console\Command;

class TestItilMetrics extends Command
{
    protected $signature = 'itil:test';
    protected $description = 'Test ITIL metrics functions';

    public function handle()
    {
        $this->info('Testing ITIL Dashboard metrics...');

        try {
            $this->info('1. Testing getIncidentMetrics...');
            $incidents = ItilDashboard::getIncidentMetrics();
            $this->line('✓ getIncidentMetrics works');

            $this->info('2. Testing getResolutionTimeMetrics...');
            $resolution = ItilDashboard::getResolutionTimeMetrics();
            $this->line('✓ getResolutionTimeMetrics works');

            $this->info('3. Testing getCategoryDistribution...');
            $categories = ItilDashboard::getCategoryDistribution();
            $this->line('✓ getCategoryDistribution works');

            $this->info('4. Testing getWorkloadAnalysis...');
            $workload = ItilDashboard::getWorkloadAnalysis();
            $this->line('✓ getWorkloadAnalysis works');

            $this->info('5. Testing getUserSatisfactionMetrics...');
            $satisfaction = ItilDashboard::getUserSatisfactionMetrics();
            $this->line('✓ getUserSatisfactionMetrics works');

            $this->info('6. Testing getServiceAvailabilityMetrics...');
            $availability = ItilDashboard::getServiceAvailabilityMetrics();
            $this->line('✓ getServiceAvailabilityMetrics works');

            $this->info('7. Testing getTrendAnalysis...');
            $trends = ItilDashboard::getTrendAnalysis(7);
            $this->line('✓ getTrendAnalysis works');

            $this->info('8. Testing Ticket query directly...');
            $tickets = Ticket::with('categorias')->count();
            $this->line("✓ Found {$tickets} tickets");

            $this->info('All ITIL metrics tests passed!');

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            return 1;
        }

        return 0;
    }
}
