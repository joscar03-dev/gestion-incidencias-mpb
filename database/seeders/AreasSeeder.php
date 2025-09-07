<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;

class AreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏢 Iniciando creación de estructura organizacional...');

        // Limpiar áreas existentes (excepto Administración si existe)
        $this->command->info('🧹 Verificando áreas existentes...');

        $adminArea = Area::where('nombre', 'Administración')->first();
        if ($adminArea) {
            $this->command->line("ℹ️  Área 'Administración' ya existe, se conservará.");
        }

        // Crear estructura completa
        $this->createOrganizationalStructure();

        $this->command->info('✅ Estructura organizacional creada exitosamente!');
        $this->showAreasSummary();
    }

    private function createOrganizationalStructure()
    {
        // Estructura completa de la organización
        $estructura = [
            // ÓRGANOS DE GOBIERNO
            [
                'nombre' => 'Órganos de Gobierno',
                'descripcion' => 'Órganos principales de gobierno municipal',
                'hijas' => [
                    [
                        'nombre' => 'CONCEJO MUNICIPAL',
                        'descripcion' => 'Órgano normativo, fiscalizador y de control político'
                    ],
                    [
                        'nombre' => 'ALCALDÍA',
                        'descripcion' => 'Órgano ejecutivo del gobierno municipal'
                    ]
                ]
            ],

            // ÓRGANOS DE CONTROL
            [
                'nombre' => 'Órganos de Control',
                'descripcion' => 'Órganos de control y fiscalización institucional',
                'hijas' => [
                    [
                        'nombre' => 'ÓRGANO DE CONTROL INSTITUCIONAL',
                        'descripcion' => 'Control interno de la gestión institucional'
                    ],
                    [
                        'nombre' => 'PROCURADURÍA PÚBLICA MUNICIPAL',
                        'descripcion' => 'Defensa legal de los intereses del Estado'
                    ]
                ]
            ],

            // GERENCIA MUNICIPAL
            [
                'nombre' => 'GERENCIA MUNICIPAL',
                'descripcion' => 'Órgano de dirección y gestión administrativa',
                'hijas' => [
                    [
                        'nombre' => 'OFICINA DE SECRETARÍA GENERAL: ATENCIÓN AL CIUDADANO Y GESTIÓN DOCUMENTARIA',
                        'descripcion' => 'Gestión documental y atención ciudadana',
                        'hijas' => [
                            [
                                'nombre' => 'UNIDAD DE TRÁMITE DOCUMENTARIO (TRÁMIFACIL)',
                                'descripcion' => 'Simplificación y facilitación de trámites'
                            ],
                            [
                                'nombre' => 'UNIDAD DE ARCHIVO GENERAL',
                                'descripcion' => 'Custodia y conservación de documentos'
                            ]
                        ]
                    ],
                    [
                        'nombre' => 'UNIDAD DE RELACIONES PÚBLICAS E IMAGEN INSTITUCIONAL',
                        'descripcion' => 'Comunicación externa e imagen corporativa'
                    ],
                    [
                        'nombre' => 'OFICINA GENERAL DE ADMINISTRACIÓN',
                        'descripcion' => 'Gestión administrativa y financiera',
                        'hijas' => [
                            [
                                'nombre' => 'OFICINA DE CONTABILIDAD',
                                'descripcion' => 'Registro y control contable'
                            ],
                            [
                                'nombre' => 'OFICINA DE TESORERÍA',
                                'descripcion' => 'Manejo de fondos y pagos'
                            ],
                            [
                                'nombre' => 'OFICINA DE ABASTECIMIENTO',
                                'descripcion' => 'Adquisiciones y suministros'
                            ],
                            [
                                'nombre' => 'OFICINA DE BIENES PATRIMONIALES',
                                'descripcion' => 'Control y administración del patrimonio'
                            ],
                            [
                                'nombre' => 'OFICINA DE TECNOLOGÍAS DE LA INFORMACIÓN',
                                'descripcion' => 'Gestión de sistemas y tecnología'
                            ],
                            [
                                'nombre' => 'OFICINA DE GESTIÓN DE RECURSOS HUMANOS',
                                'descripcion' => 'Administración del personal'
                            ],
                            [
                                'nombre' => 'Unidad de Gestión de la Calidad de Servicio',
                                'descripcion' => 'Mejora continua y calidad en servicios'
                            ]
                        ]
                    ]
                ]
            ],

            // GERENCIA DE ADMINISTRACIÓN TRIBUTARIA
            [
                'nombre' => 'Gerencia de Administración Tributaria',
                'descripcion' => 'Gestión y administración de tributos municipales',
                'hijas' => [
                    [
                        'nombre' => 'SUB GERENCIA DE RENTAS',
                        'descripcion' => 'Administración de ingresos y recaudación tributaria'
                    ],
                    [
                        'nombre' => 'SUB GERENCIA DE FISCALIZACIÓN TRIBUTARIA',
                        'descripcion' => 'Control y fiscalización del cumplimiento tributario'
                    ],
                    [
                        'nombre' => 'SUB GERENCIA DE EJECUCIÓN',
                        'descripcion' => 'Ejecución coactiva de deudas tributarias'
                    ]
                ]
            ],

            // GERENCIA DE SERVICIOS MUNICIPALES
            [
                'nombre' => 'Gerencia de Servicios Municipales',
                'descripcion' => 'Gestión de servicios públicos y ciudadanos',
                'hijas' => [
                    [
                        'nombre' => 'SUB GERENCIA DE SERVICIOS PÚBLICOS',
                        'descripcion' => 'Administración de servicios públicos esenciales',
                        'hijas' => [
                            [
                                'nombre' => 'Área de Registro Civil y Cementerio',
                                'descripcion' => 'Servicios de registro civil y administración cementerial'
                            ],
                            [
                                'nombre' => 'Área de Salubridad e Higiene',
                                'descripcion' => 'Control sanitario y de higiene pública'
                            ],
                            [
                                'nombre' => 'Área de Tránsito y Transportes',
                                'descripcion' => 'Regulación de tránsito y transporte público'
                            ]
                        ]
                    ],
                    [
                        'nombre' => 'SUB GERENCIA DE PARTICIPACIÓN Y SEGURIDAD CIUDADANA',
                        'descripcion' => 'Participación ciudadana y seguridad municipal',
                        'hijas' => [
                            [
                                'nombre' => 'Área de Gestión de Riesgo de Desastres (Defensa Civil)',
                                'descripcion' => 'Prevención y atención de emergencias y desastres'
                            ],
                            [
                                'nombre' => 'Área de Seguridad Ciudadana y Serenazgo',
                                'descripcion' => 'Servicios de seguridad ciudadana y serenazgo municipal'
                            ],
                            [
                                'nombre' => 'Área de Policía Municipal',
                                'descripcion' => 'Control y fiscalización municipal'
                            ]
                        ]
                    ]
                ]
            ],

            // GERENCIA DE GESTIÓN AMBIENTAL Y RESIDUOS SÓLIDOS
            [
                'nombre' => 'Gerencia de Gestión Ambiental y Residuos Sólidos',
                'descripcion' => 'Gestión ambiental y manejo integral de residuos sólidos',
                'hijas' => [
                    [
                        'nombre' => 'SUB GERENCIA AMBIENTAL',
                        'descripcion' => 'Gestión y protección del medio ambiente',
                        'hijas' => [
                            [
                                'nombre' => 'Área de Programas y Jardines',
                                'descripcion' => 'Mantenimiento de áreas verdes y programas ambientales'
                            ],
                            [
                                'nombre' => 'Área Técnica Municipal de Saneamiento Básico Rural (ATM)',
                                'descripcion' => 'Saneamiento básico en zonas rurales'
                            ]
                        ]
                    ],
                    [
                        'nombre' => 'SUB GERENCIA DE RESIDUOS SÓLIDOS',
                        'descripcion' => 'Manejo integral de residuos sólidos municipales',
                        'hijas' => [
                            [
                                'nombre' => 'Área de Tratamiento, Recolección y Disposición Final de Residuos Sólidos',
                                'descripcion' => 'Operaciones de recolección, tratamiento y disposición final'
                            ],
                            [
                                'nombre' => 'Área de Educación, Intermedia y Fiscalización de Residuos Sólidos',
                                'descripcion' => 'Educación ambiental y fiscalización de residuos'
                            ],
                            [
                                'nombre' => 'Área de Gestión de Maquinaria Mediana y Pesada de Residuos Sólidos',
                                'descripcion' => 'Administración de equipos y maquinaria para residuos sólidos'
                            ]
                        ]
                    ]
                ]
            ],

            // GERENCIA DE DESARROLLO TERRITORIAL E INFRAESTRUCTURA
            [
                'nombre' => 'Gerencia de Desarrollo Territorial e Infraestructura',
                'descripcion' => 'Planificación territorial y desarrollo de infraestructura',
                'hijas' => [
                    [
                        'nombre' => 'SUB GERENCIA DE DESARROLLO TERRITORIAL',
                        'descripcion' => 'Planificación y desarrollo urbano territorial',
                        'hijas' => [
                            [
                                'nombre' => 'Área de Obras, Autorizaciones, Licencias de Edificación y Habilitaciones Urbanas',
                                'descripcion' => 'Tramitación de licencias, autorizaciones y habilitaciones'
                            ],
                            [
                                'nombre' => 'Área de Catastro',
                                'descripcion' => 'Registro y control catastral municipal'
                            ]
                        ]
                    ],
                    [
                        'nombre' => 'SUB GERENCIA DE INFRAESTRUCTURA',
                        'descripcion' => 'Desarrollo y mantenimiento de infraestructura municipal',
                        'hijas' => [
                            [
                                'nombre' => 'Área de Obras Públicas',
                                'descripcion' => 'Ejecución y supervisión de obras públicas municipales'
                            ],
                            [
                                'nombre' => 'Área de Formulaciones de Estudios y Proyectos (OPI)',
                                'descripcion' => 'Formulación y evaluación de proyectos de inversión pública'
                            ]
                        ]
                    ]
                ]
            ],

            // GERENCIA DE DESARROLLO SOCIAL
            [
                'nombre' => 'Gerencia de Desarrollo Social',
                'descripcion' => 'Promoción del desarrollo social y programas de inclusión',
                'hijas' => [
                    [
                        'nombre' => 'SUB GERENCIA DE SERVICIOS SOCIALES',
                        'descripcion' => 'Gestión de servicios sociales y educativos',
                        'hijas' => [
                            [
                                'nombre' => 'Área de Educación, Cultura, Programas Sociales',
                                'descripcion' => 'Promoción educativa, cultural y programas de desarrollo social'
                            ],
                            [
                                'nombre' => 'Área de DEMUNA',
                                'descripcion' => 'Defensoría Municipal del Niño y del Adolescente'
                            ]
                        ]
                    ],
                    [
                        'nombre' => 'SUB GERENCIA DE PROGRAMAS SOCIALES',
                        'descripcion' => 'Administración de programas sociales municipales',
                        'hijas' => [
                            [
                                'nombre' => 'Área de Vaso de Leche y Comedores Populares',
                                'descripcion' => 'Gestión del programa Vaso de Leche y comedores populares'
                            ],
                            [
                                'nombre' => 'Área local de Empadronamiento (SISFOH)',
                                'descripcion' => 'Sistema de Focalización de Hogares - empadronamiento local'
                            ],
                            [
                                'nombre' => 'Área de OMAPED y CIAM',
                                'descripcion' => 'Oficina Municipal de Atención a la Persona con Discapacidad y Centro Integral del Adulto Mayor'
                            ],
                            [
                                'nombre' => 'Área de Organización Social Urbana y Rural',
                                'descripcion' => 'Fortalecimiento de organizaciones sociales urbanas y rurales'
                            ]
                        ]
                    ]
                ]
            ],

            // GERENCIA DE DESARROLLO ECONÓMICO
            [
                'nombre' => 'Gerencia de Desarrollo Económico',
                'descripcion' => 'Promoción del desarrollo económico local y productivo',
                'hijas' => [
                    [
                        'nombre' => 'SUB GERENCIA DE DESARROLLO ECONÓMICO Y PRODUCTIVO',
                        'descripcion' => 'Fomento del desarrollo económico y productivo local',
                        'hijas' => [
                            [
                                'nombre' => 'Área de Turismo, Artesanía y Promoción Empresarial',
                                'descripcion' => 'Promoción turística, artesanal y desarrollo empresarial'
                            ],
                            [
                                'nombre' => 'Área de Desarrollo Agropecuario',
                                'descripcion' => 'Fomento y desarrollo del sector agropecuario'
                            ]
                        ]
                    ],
                    [
                        'nombre' => 'SUB GERENCIA DE LICENCIAS Y CONTROL SANITARIO, COMERCIO Y FISCALIZACIÓN',
                        'descripcion' => 'Control sanitario, comercial y fiscalización municipal',
                        'hijas' => [
                            [
                                'nombre' => 'Área de Licencias de Funcionamiento',
                                'descripcion' => 'Tramitación y otorgamiento de licencias de funcionamiento'
                            ],
                            [
                                'nombre' => 'Área de Mercado y Camal',
                                'descripcion' => 'Administración de mercados municipales y camal'
                            ],
                            [
                                'nombre' => 'Área de Control Sanitario',
                                'descripcion' => 'Control y vigilancia sanitaria de establecimientos'
                            ],
                            [
                                'nombre' => 'Área de Comercialización',
                                'descripcion' => 'Regulación y promoción de actividades comerciales'
                            ],
                            [
                                'nombre' => 'Área de Fiscalización',
                                'descripcion' => 'Fiscalización y control del cumplimiento normativo'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Crear áreas recursivamente
        foreach ($estructura as $areaData) {
            $this->createAreaWithChildren($areaData);
        }
    }

    private function createAreaWithChildren($areaData, $parentId = null)
    {
        // Crear área padre
        $area = Area::firstOrCreate(
            ['nombre' => $areaData['nombre']],
            [
                'nombre' => $areaData['nombre'],
                'descripcion' => $areaData['descripcion'],
                'parent_id' => $parentId
            ]
        );

        $level = $parentId ? ($this->getAreaLevel($parentId) + 1) : 1;
        $indent = str_repeat('  ', $level - 1);
        $icon = $this->getAreaIcon($level);

        $this->command->line("{$indent}{$icon} {$area->nombre}");

        // Crear áreas hijas si existen
        if (isset($areaData['hijas'])) {
            foreach ($areaData['hijas'] as $hijaData) {
                $this->createAreaWithChildren($hijaData, $area->id);
            }
        }

        return $area;
    }

    private function getAreaLevel($parentId, $level = 1)
    {
        if (!$parentId) return $level;

        $parent = Area::find($parentId);
        if (!$parent || !$parent->parent_id) {
            return $level + 1;
        }

        return $this->getAreaLevel($parent->parent_id, $level + 1);
    }

    private function getAreaIcon($level)
    {
        return match ($level) {
            1 => '🏛️',  // Nivel superior
            2 => '🏢',  // Segundo nivel
            3 => '📂',  // Tercer nivel
            4 => '📁',  // Cuarto nivel
            default => '📄'
        };
    }

    private function showAreasSummary()
    {
        $this->command->info('📊 RESUMEN DE ÁREAS CREADAS:');
        $this->command->line('');

        $totalAreas = Area::count();
        $areasPadre = Area::whereNull('parent_id')->count();
        $areasHijas = Area::whereNotNull('parent_id')->count();

        $this->command->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total de Áreas', $totalAreas],
                ['Áreas Principales', $areasPadre],
                ['Sub-áreas', $areasHijas],
            ]
        );

        $this->command->info('🌳 ESTRUCTURA JERÁRQUICA:');
        $this->showHierarchy();
    }

    private function showHierarchy()
    {
        $areasPrincipales = Area::whereNull('parent_id')->get();

        foreach ($areasPrincipales as $area) {
            $this->command->line("🏛️  {$area->nombre}");
            $this->showChildren($area, 1);
        }
    }

    private function showChildren($area, $level)
    {
        $children = $area->hijas;
        $indent = str_repeat('  ', $level);
        $icon = $this->getAreaIcon($level + 1);

        foreach ($children as $child) {
            $this->command->line("{$indent}{$icon} {$child->nombre}");
            if ($child->hijas->count() > 0) {
                $this->showChildren($child, $level + 1);
            }
        }
    }
}
