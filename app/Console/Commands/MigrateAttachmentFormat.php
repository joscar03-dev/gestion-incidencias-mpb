<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;

class MigrateAttachmentFormat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:migrate-attachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra el formato de attachments de tickets del formato anterior al nuevo formato compatible con Filament';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migración de formato de attachments...');

        $tickets = Ticket::whereNotNull('attachment')
            ->where('attachment', '!=', '')
            ->where('attachment', '!=', '[]')
            ->get();

        if ($tickets->isEmpty()) {
            $this->info('No se encontraron tickets con attachments para migrar.');
            return;
        }

        $this->info("Encontrados {$tickets->count()} tickets con attachments.");

        $migrated = 0;
        $errors = 0;

        foreach ($tickets as $ticket) {
            try {
                $attachment = $ticket->getAttributes()['attachment']; // Obtener valor crudo
                
                if (is_string($attachment)) {
                    $decoded = json_decode($attachment, true);
                    
                    if (is_array($decoded)) {
                        $needsMigration = false;
                        $newFormat = [];
                        
                        foreach ($decoded as $archivo) {
                            if (is_array($archivo) && isset($archivo['ruta'])) {
                                // Formato anterior - necesita migración
                                $needsMigration = true;
                                $newFormat[] = $archivo['ruta'];
                            } elseif (is_string($archivo)) {
                                // Ya está en formato nuevo
                                $newFormat[] = $archivo;
                            }
                        }
                        
                        if ($needsMigration) {
                            $ticket->update(['attachment' => $newFormat]);
                            $migrated++;
                            $this->line("✓ Ticket #{$ticket->id} migrado: " . count($newFormat) . " archivos");
                        } else {
                            $this->line("- Ticket #{$ticket->id} ya está en formato correcto");
                        }
                    }
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("✗ Error migrando ticket #{$ticket->id}: " . $e->getMessage());
            }
        }

        $this->info("Migración completada:");
        $this->info("- Tickets migrados: {$migrated}");
        $this->info("- Errores: {$errors}");
        
        if ($errors > 0) {
            $this->warn("Algunos tickets no pudieron ser migrados. Revisa los errores anteriores.");
        }
    }
}
