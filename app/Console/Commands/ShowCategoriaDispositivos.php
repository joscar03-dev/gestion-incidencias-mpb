<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CategoriaDispositivo;

class ShowCategoriaDispositivos extends Command
{
    protected $signature = 'dispositivos:show-categories';
    protected $description = 'Muestra todas las categorías de dispositivos creadas';

    public function handle()
    {
        $this->info('📱 CATEGORÍAS DE DISPOSITIVOS');
        $this->info('=============================');

        $categorias = CategoriaDispositivo::orderBy('nombre')->get();

        if ($categorias->count() > 0) {
            $grupos = [
                'Equipos de Computación' => ['Computadoras de Escritorio', 'Laptops y Portátiles', 'Tablets y Dispositivos Móviles'],
                'Servidores e Infraestructura' => ['Servidores', 'Equipos de Red', 'Almacenamiento'],
                'Periféricos' => ['Monitores y Pantallas', 'Teclados y Mouse', 'Dispositivos de Audio'],
                'Impresión y Digitalización' => ['Impresoras', 'Escáneres y Digitalizadores'],
                'Comunicaciones' => ['Teléfonos IP', 'Equipos de Videoconferencia'],
                'Seguridad' => ['Sistemas de Seguridad', 'Control de Acceso'],
                'Especializados' => ['Equipos de Laboratorio', 'Dispositivos IoT', 'Equipos de Backup'],
                'Móviles Corporativos' => ['Smartphones Corporativos', 'Equipos de Campo'],
                'Tecnologías Avanzadas' => ['Equipos de Virtualización', 'Dispositivos de Desarrollo']
            ];

            foreach ($grupos as $grupo => $nombresCategoria) {
                $this->line('');
                $this->info($grupo);
                $this->line(str_repeat('-', strlen($grupo)));

                foreach ($nombresCategoria as $nombreCategoria) {
                    $categoria = $categorias->firstWhere('nombre', $nombreCategoria);
                    if ($categoria) {
                        $this->line("• {$categoria->nombre}");
                        $this->line("  {$categoria->descripcion}");
                    }
                }
            }

            // Mostrar categorías no agrupadas
            $categoriasAgrupadas = collect($grupos)->flatten();
            $categoriasNoAgrupadas = $categorias->whereNotIn('nombre', $categoriasAgrupadas);

            if ($categoriasNoAgrupadas->count() > 0) {
                $this->line('');
                $this->info('Otras Categorías');
                $this->line('----------------');
                foreach ($categoriasNoAgrupadas as $categoria) {
                    $this->line("• {$categoria->nombre}");
                    $this->line("  {$categoria->descripcion}");
                }
            }

        } else {
            $this->warn('No hay categorías de dispositivos creadas.');
        }

        $this->line('');
        $this->info("✅ Total de categorías: {$categorias->count()}");

        return Command::SUCCESS;
    }
}
