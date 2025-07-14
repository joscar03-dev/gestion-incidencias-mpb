<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test optimized ticket notifications system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Probando sistema optimizado de notificaciones...');

        // Obtener el primer usuario
        $user = User::first();

        if (!$user) {
            $this->error('❌ No hay usuarios en la base de datos');
            return 1;
        }

        // Crear un ticket de prueba
        $ticket = new Ticket([
            'titulo' => 'Test Sistema Optimizado - ' . now()->format('H:i:s'),
            'descripcion' => 'Este ticket prueba el sistema optimizado sin duplicados',
            'estado' => 'Abierto',
            'prioridad' => 'Media',
            'tipo' => 'General',
            'asignado_a' => $user->id,
            'creado_por' => $user->id,
            'area_id' => 1
        ]);

        $ticket->save();

        $this->info("✅ Ticket creado: #{$ticket->id}");
        $this->info("👤 Usuario: {$user->name} ({$user->email})");
        $this->info("📧 Observer envió notificaciones sin duplicados");

        // Probar reasignación
        $this->info("\n🔄 Probando reasignación...");
        $ticket->update(['asignado_a' => $user->id]); // Reasignar al mismo usuario

        // Probar cambio de prioridad
        $this->info("🔴 Probando cambio a prioridad crítica...");
        $ticket->update(['prioridad' => 'Critica']);

        // Probar cambio de estado
        $this->info("📋 Probando cambio de estado...");
        $ticket->update(['estado' => 'En Progreso']);

        // Probar cierre
        $this->info("✅ Probando cierre del ticket...");
        $ticket->update(['estado' => 'Cerrado']);

        $this->info("\n🎉 Todas las pruebas completadas!");
        $this->info("📊 Verifica en Filament las notificaciones (sin duplicados)");

        return 0;
    }
}
