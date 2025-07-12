<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoriaDispositivo;

class CategoriaDispositivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            // Equipos de Computación
            [
                'nombre' => 'Computadoras de Escritorio',
                'descripcion' => 'PCs de escritorio, estaciones de trabajo, all-in-one y equipos de sobremesa para uso empresarial y personal'
            ],
            [
                'nombre' => 'Laptops y Portátiles',
                'descripcion' => 'Computadoras portátiles, ultrabooks, notebooks, laptops gaming y equipos móviles para trabajo'
            ],
            [
                'nombre' => 'Tablets y Dispositivos Móviles',
                'descripcion' => 'Tablets empresariales, iPads, dispositivos Android, e-readers y equipos móviles corporativos'
            ],

            // Servidores y Infraestructura
            [
                'nombre' => 'Servidores',
                'descripcion' => 'Servidores físicos, blade servers, rack servers, torres de servidor y equipos de centro de datos'
            ],
            [
                'nombre' => 'Equipos de Red',
                'descripcion' => 'Switches, routers, firewalls, access points, balanceadores de carga y equipos de conectividad'
            ],
            [
                'nombre' => 'Almacenamiento',
                'descripcion' => 'NAS, SAN, arrays de discos, unidades externas, sistemas de backup y almacenamiento empresarial'
            ],

            // Periféricos de Entrada
            [
                'nombre' => 'Monitores y Pantallas',
                'descripcion' => 'Monitores LCD, LED, OLED, pantallas táctiles, proyectores y displays corporativos'
            ],
            [
                'nombre' => 'Teclados y Mouse',
                'descripcion' => 'Teclados mecánicos, inalámbricos, mouse ópticos, trackballs y dispositivos de entrada'
            ],
            [
                'nombre' => 'Dispositivos de Audio',
                'descripcion' => 'Auriculares, altavoces, micrófonos, sistemas de audio y equipos de conferencia'
            ],

            // Impresión y Digitalización
            [
                'nombre' => 'Impresoras',
                'descripcion' => 'Impresoras láser, inkjet, matriciales, multifuncionales y equipos de impresión empresarial'
            ],
            [
                'nombre' => 'Escáneres y Digitalizadores',
                'descripcion' => 'Escáneres de documentos, digitalizadores, lectores de códigos de barra y equipos de captura'
            ],

            // Comunicaciones
            [
                'nombre' => 'Teléfonos IP',
                'descripcion' => 'Teléfonos VoIP, sistemas de telefonía, centrales telefónicas y equipos de comunicación'
            ],
            [
                'nombre' => 'Equipos de Videoconferencia',
                'descripcion' => 'Cámaras web, sistemas de videoconferencia, pantallas interactivas y equipos de colaboración'
            ],

            // Seguridad y Control de Acceso
            [
                'nombre' => 'Sistemas de Seguridad',
                'descripcion' => 'Cámaras de seguridad, DVR, NVR, sistemas de alarma y equipos de vigilancia'
            ],
            [
                'nombre' => 'Control de Acceso',
                'descripcion' => 'Lectores biométricos, tarjetas de acceso, cerraduras electrónicas y sistemas de identificación'
            ],

            // Equipos Especializados
            [
                'nombre' => 'Equipos de Laboratorio',
                'descripcion' => 'Equipos científicos, instrumentos de medición, balanzas digitales y dispositivos especializados'
            ],
            [
                'nombre' => 'Dispositivos IoT',
                'descripcion' => 'Sensores inteligentes, dispositivos conectados, automatización y equipos de Internet de las Cosas'
            ],
            [
                'nombre' => 'Equipos de Backup',
                'descripcion' => 'UPS, sistemas de alimentación ininterrumpida, generadores y equipos de respaldo energético'
            ],

            // Equipos Móviles Corporativos
            [
                'nombre' => 'Smartphones Corporativos',
                'descripcion' => 'Teléfonos inteligentes empresariales, dispositivos móviles de trabajo y comunicación corporativa'
            ],
            [
                'nombre' => 'Equipos de Campo',
                'descripcion' => 'Dispositivos portátiles industriales, escáneres móviles, tablets ruguerizadas y equipos de campo'
            ],

            // Virtualización y Cloud
            [
                'nombre' => 'Equipos de Virtualización',
                'descripcion' => 'Hypervisors, thin clients, zero clients y equipos para entornos virtualizados'
            ],
            [
                'nombre' => 'Dispositivos de Desarrollo',
                'descripcion' => 'Estaciones de desarrollo, equipos de testing, servidores de desarrollo y herramientas especializadas'
            ]
        ];

        foreach ($categorias as $categoria) {
            CategoriaDispositivo::firstOrCreate(
                ['nombre' => $categoria['nombre']],
                ['descripcion' => $categoria['descripcion']]
            );
        }

        $this->command->info('✅ Categorías de dispositivos creadas exitosamente!');
        $this->command->info('📱 Total categorías: ' . CategoriaDispositivo::count());
    }
}
