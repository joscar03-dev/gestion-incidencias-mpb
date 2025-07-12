<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración ITIL
    |--------------------------------------------------------------------------
    |
    | Esta configuración define los parámetros del framework ITIL v4
    | para la gestión de servicios de TI.
    |
    */

    'version' => '4.0',

    /*
    |--------------------------------------------------------------------------
    | Configuración de SLA (Service Level Agreement)
    |--------------------------------------------------------------------------
    */
    
    'sla' => [
        'priority_times' => [
            'critica' => [
                'response_minutes' => 15,
                'resolution_hours' => 2,
                'escalation_threshold' => 1, // horas antes de escalar
            ],
            'alta' => [
                'response_minutes' => 30,
                'resolution_hours' => 4,
                'escalation_threshold' => 2,
            ],
            'media' => [
                'response_minutes' => 60,
                'resolution_hours' => 24,
                'escalation_threshold' => 12,
            ],
            'baja' => [
                'response_minutes' => 240,
                'resolution_hours' => 72,
                'escalation_threshold' => 48,
            ],
        ],

        'business_hours' => [
            'start' => '08:00',
            'end' => '18:00',
            'timezone' => 'America/Lima',
            'weekdays_only' => true,
        ],

        'escalation_levels' => [
            1 => 'Supervisor',
            2 => 'Gerente de TI',
            3 => 'Director',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Categorías de Servicios ITIL
    |--------------------------------------------------------------------------
    */

    'service_categories' => [
        'incident_management' => [
            'name' => 'Gestión de Incidentes',
            'description' => 'Restaurar el servicio normal lo más rápido posible',
            'enabled' => true,
        ],
        'service_request' => [
            'name' => 'Solicitudes de Servicio',
            'description' => 'Gestionar solicitudes de usuarios',
            'enabled' => true,
        ],
        'change_management' => [
            'name' => 'Gestión de Cambios',
            'description' => 'Controlar el ciclo de vida de todos los cambios',
            'enabled' => true,
        ],
        'problem_management' => [
            'name' => 'Gestión de Problemas',
            'description' => 'Reducir el impacto de incidentes',
            'enabled' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Métricas
    |--------------------------------------------------------------------------
    */

    'metrics' => [
        'kpis' => [
            'incident_resolution_rate' => [
                'target' => 95, // porcentaje
                'critical_threshold' => 85,
            ],
            'sla_compliance' => [
                'target' => 98,
                'critical_threshold' => 90,
            ],
            'first_call_resolution' => [
                'target' => 70,
                'critical_threshold' => 50,
            ],
            'customer_satisfaction' => [
                'target' => 4.5, // escala 1-5
                'critical_threshold' => 3.5,
            ],
        ],

        'availability_targets' => [
            'critical_services' => 99.9,
            'important_services' => 99.5,
            'standard_services' => 99.0,
        ],

        'mttr_targets' => [
            'critica' => 2, // horas
            'alta' => 4,
            'media' => 24,
            'baja' => 72,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Reportes
    |--------------------------------------------------------------------------
    */

    'reports' => [
        'auto_generation' => [
            'enabled' => true,
            'schedule' => 'daily', // daily, weekly, monthly
            'recipients' => [
                'ti@empresa.com',
                'gerencia@empresa.com',
            ],
        ],

        'export_formats' => [
            'excel' => true,
            'pdf' => true,
            'csv' => false,
            'json' => false,
        ],

        'dashboard_refresh_interval' => 300, // segundos
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Notificaciones
    |--------------------------------------------------------------------------
    */

    'notifications' => [
        'sla_breach_warning' => [
            'enabled' => true,
            'threshold_percentage' => 80, // % del tiempo SLA transcurrido
        ],

        'escalation_notifications' => [
            'enabled' => true,
            'channels' => ['email', 'database'],
        ],

        'daily_summary' => [
            'enabled' => true,
            'time' => '18:00',
            'recipients' => ['supervisores', 'gerentes'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Workflow
    |--------------------------------------------------------------------------
    */

    'workflow' => [
        'auto_assignment' => [
            'enabled' => true,
            'round_robin' => true,
            'skill_based' => false,
        ],

        'approval_workflows' => [
            'change_management' => [
                'normal_change' => ['supervisor', 'cab'],
                'emergency_change' => ['gerente_ti'],
                'standard_change' => [], // auto-aprobado
            ],
        ],

        'closure_requirements' => [
            'customer_confirmation' => false,
            'root_cause_analysis' => ['problem_management'],
            'documentation_required' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Integración
    |--------------------------------------------------------------------------
    */

    'integrations' => [
        'monitoring_tools' => [
            'enabled' => false,
            'auto_incident_creation' => false,
        ],

        'cmdb' => [
            'enabled' => false,
            'sync_configuration_items' => false,
        ],

        'knowledge_base' => [
            'enabled' => true,
            'auto_suggestions' => true,
        ],
    ],

];
