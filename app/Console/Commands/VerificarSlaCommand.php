<?php

namespace App\Console\Commands;

use App\Jobs\VerificarSlaTickets;
use Illuminate\Console\Command;

class VerificarSlaCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tickets:verificar-sla {--sync : Ejecutar sincronicamente}';

    /**
     * The console command description.
     */
    protected $description = 'Verifica el SLA de todos los tickets activos y ejecuta escalamientos automáticos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando verificación de SLA de tickets...');

        if ($this->option('sync')) {
            // Ejecutar sincronicamente
            $job = new VerificarSlaTickets();
            $job->handle();
            $this->info('Verificación completada sincronicamente');
        } else {
            // Despachar a la cola
            VerificarSlaTickets::dispatch();
            $this->info('Job de verificación despachado a la cola');
        }

        return 0;
    }
}
