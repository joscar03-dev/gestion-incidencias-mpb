<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Categoria;

class ShowItilCategories extends Command
{
    protected $signature = 'itil:show-categories';
    protected $description = 'Muestra todas las categorías ITIL creadas';

    public function handle()
    {
        $this->info('📋 CATEGORÍAS ITIL CREADAS');
        $this->info('================================');

        $tipos = ['incidente', 'solicitud_servicio', 'cambio', 'problema'];

        foreach ($tipos as $tipo) {
            $categorias = Categoria::where('tipo_categoria', $tipo)
                ->where('itil_category', true)
                ->get();

            if ($categorias->count() > 0) {
                $this->line('');
                $this->info(strtoupper(str_replace('_', ' ', $tipo)) . ' (' . $categorias->count() . ')');
                $this->line(str_repeat('-', 50));

                foreach ($categorias as $categoria) {
                    $this->line(sprintf(
                        '• %s (Prioridad: %s)',
                        $categoria->nombre,
                        $categoria->prioridad_default
                    ));
                }
            }
        }

        $total = Categoria::where('itil_category', true)->count();
        $this->line('');
        $this->info("✅ Total de categorías ITIL: {$total}");

        return Command::SUCCESS;
    }
}
