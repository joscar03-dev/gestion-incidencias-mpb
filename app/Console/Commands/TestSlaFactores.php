<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sla;
use App\Models\SlaPrioridadFactor;
use App\Models\SlaTipoFactor;

class TestSlaFactores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:sla-factores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el sistema de factores SLA administrables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Probando el sistema de factores SLA administrables...');

        // Mostrar factores de prioridad
        $this->info("\n=== Factores de Prioridad ===");
        $factoresPrioridad = SlaPrioridadFactor::activos()->ordenados()->get();
        foreach ($factoresPrioridad as $factor) {
            $this->line("- {$factor->codigo}: {$factor->nombre} (Factor: {$factor->factor})");
        }

        // Mostrar factores de tipo
        $this->info("\n=== Factores de Tipo ===");
        $factoresTipo = SlaTipoFactor::activos()->ordenados()->get();
        foreach ($factoresTipo as $factor) {
            $this->line("- {$factor->codigo}: {$factor->nombre} (Factor: {$factor->factor})");
        }

        // Probar métodos de compatibilidad
        $this->info("\n=== Prueba de Compatibilidad ===");
        $factoresPrioridadArray = Sla::getFactoresPrioridad();
        $this->info("Factores de prioridad vía array: " . count($factoresPrioridadArray));

        $factoresTipoArray = Sla::getFactoresTipo();
        $this->info("Factores de tipo vía array: " . count($factoresTipoArray));

        // Probar cálculo de factor individual
        $this->info("
=== Prueba de Cálculo Individual ===");
        $factorCritica = SlaPrioridadFactor::obtenerFactorPorCodigo('critica');
        $this->info("Factor para 'critica': " . $factorCritica);

        $factorIncidente = SlaTipoFactor::obtenerFactorPorCodigo('incidente');
        $this->info("Factor para 'incidente': " . $factorIncidente);

        // Probar cálculo SLA completo
        $this->info("
=== Ejemplo de Cálculo SLA Completo ===");
        $slaEjemplo = Sla::calcularParaTicket(1, 'critica', 'incidente'); // Asumiendo que área 1 existe
        if ($slaEjemplo['encontrado']) {
            $this->info("SLA para ticket crítico/incidente en área 1:");
            $this->info("- Tiempo respuesta: " . $slaEjemplo['tiempo_respuesta'] . " minutos");
            $this->info("- Tiempo resolución: " . $slaEjemplo['tiempo_resolucion'] . " minutos");
            $this->info("- Factor combinado: " . $slaEjemplo['factor_combinado']);
        } else {
            $this->info("No se encontró SLA para área 1");
        }

        $this->info("
¡Sistema de factores administrables funcionando correctamente!");
        return 0;
    }
}
