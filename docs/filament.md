# 🎛️ Documentación del Panel Administrativo Filament

Esta documentación detalla la implementación completa del panel administrativo usando Filament 3, incluyendo todos los recursos, widgets, páginas y funcionalidades. (NO USAR, SOLO EN CASO DE EMERGENCIA)

## 📋 Índice

1. [Configuración General](#configuración-general)
2. [Recursos (Resources)](#recursos-resources)
3. [Widgets](#widgets)
4. [Páginas Personalizadas](#páginas-personalizadas)
5. [Acciones y Filtros](#acciones-y-filtros)
6. [Autenticación y Autorización](#autenticación-y-autorización)
7. [Personalización](#personalización)

---

## ⚙️ Configuración General

### Configuración Base

**Archivo:** `config/filament.php`

```php
'default' => [
    'id' => 'admin',
    'path' => 'admin',
    'login' => App\Filament\Pages\Auth\Login::class,
    'domain' => null,
    'middleware' => [
        'web',
        'auth',
    ],
    'auth' => [
        'guard' => 'web',
        'pages' => [
            'login' => App\Filament\Pages\Auth\Login::class,
        ],
    ],
],
```

### Configuración del Dashboard

El panel administrativo está accesible en `/admin` y requiere autenticación. Solo usuarios con roles administrativos pueden acceder.

---

## 🗂️ Recursos (Resources)

### 1. TicketResource

**Archivo:** `app/Filament/Resources/TicketResource.php`

#### Descripción
Recurso principal para la gestión completa de tickets de soporte.

#### Configuración Base
```php
protected static ?string $model = Ticket::class;
protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
protected static ?string $navigationLabel = 'Tickets';
protected static ?string $pluralModelLabel = 'Tickets';
protected static ?string $modelLabel = 'Ticket';
```

#### Badge de Navegación
```php
public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}

public static function getNavigationBadgeColor(): string|array|null
{
    return static::getModel()::count() > 4 ? 'danger' : 'success';
}
```

#### Formulario (Form)
```php
public static function form(Form $form): Form
{
    return $form
        ->columns(3)
        ->schema([
            // Campo creado por (solo para Super Admin)
            Select::make('creado_por')
                ->label('Creado por')
                ->options(User::pluck('name', 'id')->toArray())
                ->visible(fn($record) => Auth::user()?->hasRole('Super Admin') && $record === null)
                ->required(fn($record) => Auth::user()?->hasRole('Super Admin') && $record === null)
                ->default(fn() => Auth::id())
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $user = User::find($state);
                    if ($user && $user->area_id) {
                        $set('area_id', $user->area_id);
                    }
                }),

            // Información del creador (solo lectura)
            Placeholder::make('creado_por_info')
                ->label('Creado por')
                ->content(fn($record) => $record?->creadoPor?->name ?? 'N/A')
                ->visible(fn($record) => $record !== null),

            // Campo área oculto
            Forms\Components\Hidden::make('area_id')
                ->default(fn() => Auth::user()?->area_id),

            // Información del área
            Placeholder::make('area_usuario')
                ->label('Área del Usuario que Reporta')
                ->content(function ($record, $get) {
                    if ($record) {
                        return $record->creadoPor?->area?->nombre ?? 'Sin área asignada';
                    } else {
                        if (Auth::user()?->hasRole('Super Admin')) {
                            $userId = $get('creado_por') ?? Auth::id();
                            $user = User::find($userId);
                            return $user?->area?->nombre ?? 'Sin área asignada';
                        } else {
                            return Auth::user()?->area?->nombre ?? 'Sin área asignada';
                        }
                    }
                }),

            // Campos principales
            TextInput::make('titulo')
                ->label('Título')
                ->required()
                ->autofocus()
                ->columnSpan(2),

            Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(3)
                ->columnSpan(3),

            // Prioridad con cálculo de SLA
            Select::make('prioridad')
                ->label('Prioridad')
                ->options(self::$model::PRIORIDAD)
                ->required()
                ->default('Media')
                ->reactive()
                ->afterStateUpdated(function ($state, $set, $get) {
                    // Lógica de cálculo de SLA
                    $this->actualizarSlaInfo($state, $set, $get);
                }),

            // Información de SLA calculado
            Placeholder::make('sla_info')
                ->label('SLA Calculado')
                ->content(function ($get) {
                    return $this->calcularSlaInfo($get);
                })
                ->visible(fn($get) => $get('prioridad')),

            // Estado del ticket
            Select::make('estado')
                ->label('Estado')
                ->options(self::$model::ESTADOS)
                ->default('Abierto')
                ->required()
                ->reactive(),

            // Asignación (solo para roles administrativos)
            Select::make('asignado_a')
                ->label('Asignado a')
                ->options(User::role(['Tecnico'])->pluck('name', 'id')->toArray())
                ->visible(fn() => Auth::user()?->hasRole(['Super Admin', 'Admin'])),

            // Campos de escalamiento (solo visible en edición)
            Toggle::make('escalado')
                ->label('¿Escalado?')
                ->visible(fn($record) => $record !== null)
                ->disabled(),

            DateTimePicker::make('fecha_escalamiento')
                ->label('Fecha de Escalamiento')
                ->visible(fn($record) => $record?->escalado)
                ->disabled(),

            Toggle::make('sla_vencido')
                ->label('SLA Vencido')
                ->visible(fn($record) => $record !== null)
                ->disabled(),

            // Archivo adjunto
            FileUpload::make('attachment')
                ->label('Archivo')
                ->preserveFilenames()
                ->downloadable()
                ->directory('tickets')
                ->acceptedFileTypes(['application/pdf', 'image/*'])
                ->maxSize(1024)
                ->columnSpan(2),

            // Comentario de resolución
            Textarea::make('comentario')
                ->label('Solución / Comentario')
                ->rows(3)
                ->visible(fn($get) => $get('estado') === 'Cerrado')
                ->required(fn($get) => $get('estado') === 'Cerrado')
                ->columnSpan(3),
        ]);
}
```

#### Tabla (Table)
```php
public static function table(Table $table): Table
{
    return $table
        ->modifyQueryUsing(fn(Builder $query) =>
            Auth::user()->hasRole('Super Admin') ?
                $query : $query->where('asignado_a', Auth::id())
        )
        ->defaultSort('created_at', 'desc')
        ->columns([
            // Título con descripción
            TextColumn::make('titulo')
                ->description(fn(Ticket $record): ?string => $record?->descripcion ?? null)
                ->label('Título')
                ->searchable()
                ->sortable()
                ->weight('bold'),

            // Área
            TextColumn::make('area.nombre')
                ->label('Área')
                ->searchable()
                ->sortable(),

            // Prioridad con badge
            TextColumn::make('prioridad')
                ->label('Prioridad')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Critica' => 'danger',
                    'Alta' => 'warning',
                    'Media' => 'success',
                    'Baja' => 'secondary',
                    default => 'secondary',
                })
                ->icon(fn (string $state): string => match ($state) {
                    'Critica' => 'heroicon-o-fire',
                    'Alta' => 'heroicon-o-exclamation-triangle',
                    'Media' => 'heroicon-o-information-circle',
                    'Baja' => 'heroicon-o-minus-circle',
                    default => 'heroicon-o-question-mark-circle',
                }),

            // Estado con badge
            TextColumn::make('estado')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Abierto' => 'danger',
                    'En Progreso' => 'warning',
                    'Escalado' => 'danger',
                    'Cerrado' => 'success',
                    'Cancelado' => 'gray',
                    'Archivado' => 'secondary',
                    default => 'secondary',
                })
                ->icon(fn (string $state): string => match ($state) {
                    'Abierto' => 'heroicon-o-clock',
                    'En Progreso' => 'heroicon-o-cog',
                    'Escalado' => 'heroicon-o-arrow-trending-up',
                    'Cerrado' => 'heroicon-o-check-circle',
                    'Cancelado' => 'heroicon-o-x-circle',
                    'Archivado' => 'heroicon-o-archive-box',
                    default => 'heroicon-o-question-mark-circle',
                }),

            // Estado de SLA
            TextColumn::make('estado_sla')
                ->label('SLA')
                ->getStateUsing(function (Ticket $record) {
                    return $record->getEstadoSla();
                })
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'ok' => 'success',
                    'advertencia' => 'warning',
                    'vencido' => 'danger',
                    'sin_sla' => 'secondary',
                    default => 'secondary',
                })
                ->formatStateUsing(function ($state) {
                    return match($state) {
                        'ok' => 'OK',
                        'advertencia' => 'Advertencia',
                        'vencido' => 'Vencido',
                        'sin_sla' => 'Sin SLA',
                        default => 'Desconocido'
                    };
                }),

            // Tiempo restante
            TextColumn::make('tiempo_restante')
                ->label('Tiempo Restante')
                ->getStateUsing(function (Ticket $record) {
                    $tiempo = $record->getTiempoRestanteSla('respuesta');
                    if ($tiempo === null) return 'N/A';
                    if ($tiempo <= 0) return 'Vencido';

                    $horas = floor($tiempo / 60);
                    $minutos = $tiempo % 60;

                    return $horas > 0 ? "{$horas}h {$minutos}m" : "{$minutos}m";
                })
                ->color(function (Ticket $record) {
                    $tiempo = $record->getTiempoRestanteSla('respuesta');
                    if ($tiempo === null) return 'secondary';
                    if ($tiempo <= 0) return 'danger';
                    if ($tiempo <= 30) return 'warning';
                    return 'success';
                }),

            // Escalamiento
            TextColumn::make('escalado')
                ->label('Escalado')
                ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                ->badge()
                ->color(fn (bool $state): string => $state ? 'danger' : 'success'),

            // Asignado a
            TextColumn::make('asignadoA.name')
                ->label('Asignado a')
                ->searchable(),

            // Creado por
            TextColumn::make('creadoPor.name')
                ->label('Creado por')
                ->searchable()
                ->sortable(),

            // Fecha de creación
            TextColumn::make('created_at')
                ->label('Creado')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(),

            // Fecha de escalamiento
            TextColumn::make('fecha_escalamiento')
                ->label('Fecha Escalamiento')
                ->dateTime('d/m/Y H:i')
                ->visible(fn($record) => $record?->escalado)
                ->toggleable(isToggledHiddenByDefault: true),
        ]);
}
```

#### Filtros
```php
->filters([
    // Filtro por estado
    SelectFilter::make('estado')
        ->options(self::$model::ESTADOS)
        ->multiple(),

    // Filtro por prioridad
    SelectFilter::make('prioridad')
        ->options(self::$model::PRIORIDAD)
        ->multiple(),

    // Filtro por área
    SelectFilter::make('area_id')
        ->label('Área')
        ->relationship('area', 'nombre')
        ->multiple(),

    // Filtro por escalamiento
    SelectFilter::make('escalado')
        ->label('Escalado')
        ->options([
            '1' => 'Escalado',
            '0' => 'No Escalado',
        ]),

    // Filtro por SLA vencido
    SelectFilter::make('sla_vencido')
        ->label('SLA Vencido')
        ->options([
            '1' => 'SLA Vencido',
            '0' => 'SLA Vigente',
        ]),

    // Filtro por asignado
    SelectFilter::make('asignado_a')
        ->label('Asignado a')
        ->relationship('asignadoA', 'name')
        ->multiple(),
])
```

#### Acciones de Tabla
```php
->actions([
    // Acción para escalar manualmente
    Action::make('escalar')
        ->label('Escalar')
        ->icon('heroicon-o-arrow-trending-up')
        ->color('warning')
        ->visible(fn(Ticket $record) => !$record->escalado && $record->estado !== 'Cerrado')
        ->requiresConfirmation()
        ->modalHeading('Escalar Ticket')
        ->modalDescription('¿Estás seguro de que quieres escalar este ticket?')
        ->action(function (Ticket $record) {
            $record->escalar('Escalamiento manual');
            Notification::make()
                ->title('Ticket Escalado')
                ->success()
                ->send();
        }),

    // Acción para verificar SLA
    Action::make('verificar_sla')
        ->label('Verificar SLA')
        ->icon('heroicon-o-clock')
        ->color('info')
        ->action(function (Ticket $record) {
            $escalado = $record->verificarSlaYEscalamiento();
            
            $message = $escalado ? 
                "El ticket #{$record->id} ha sido escalado por vencimiento de SLA." :
                "El ticket #{$record->id} está dentro del SLA.";
            
            Notification::make()
                ->title($escalado ? 'Ticket Escalado' : 'SLA Verificado')
                ->body($message)
                ->success()
                ->send();
        }),

    // Acciones estándar
    Tables\Actions\EditAction::make(),
    Tables\Actions\DeleteAction::make(),
    CommentsTableAction::make(),
])
```

### 2. UserResource

**Archivo:** `app/Filament/Resources/UserResource.php`

#### Descripción
Gestión completa de usuarios con roles y permisos.

#### Configuración
```php
protected static ?string $model = User::class;
protected static ?string $navigationIcon = 'heroicon-o-users';
protected static ?string $navigationGroup = 'Administración';
```

#### Formulario
```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Select::make('area_id')
                ->label('Área')
                ->relationship('area', 'nombre')
                ->required()
                ->searchable(),

            TextInput::make('password')
                ->label('Contraseña')
                ->password()
                ->required(fn($record) => $record === null)
                ->minLength(8)
                ->dehydrated(fn($state) => filled($state)),

            Select::make('roles')
                ->label('Roles')
                ->multiple()
                ->relationship('roles', 'name')
                ->preload()
                ->searchable(),

            Toggle::make('activo')
                ->label('Usuario Activo')
                ->default(true),
        ]);
}
```

### 3. AreaResource

**Archivo:** `app/Filament/Resources/AreaResource.php`

#### Descripción
Gestión de áreas organizacionales.

#### Formulario
```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(100),

            Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(3)
                ->maxLength(500),

            Toggle::make('activo')
                ->label('Área Activa')
                ->default(true),
        ]);
}
```

### 4. SlaResource

**Archivo:** `app/Filament/Resources/SlaResource.php`

#### Descripción
Configuración de SLAs por área.

#### Formulario
```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Select::make('area_id')
                ->label('Área')
                ->relationship('area', 'nombre')
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(100),

            Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(2),

            // Tiempos de respuesta
            Section::make('Tiempos de Respuesta (minutos)')
                ->schema([
                    Grid::make(4)
                        ->schema([
                            TextInput::make('tiempo_respuesta_critico')
                                ->label('Crítico')
                                ->numeric()
                                ->required()
                                ->suffix('min'),
                            TextInput::make('tiempo_respuesta_alto')
                                ->label('Alto')
                                ->numeric()
                                ->required()
                                ->suffix('min'),
                            TextInput::make('tiempo_respuesta_medio')
                                ->label('Medio')
                                ->numeric()
                                ->required()
                                ->suffix('min'),
                            TextInput::make('tiempo_respuesta_bajo')
                                ->label('Bajo')
                                ->numeric()
                                ->required()
                                ->suffix('min'),
                        ]),
                ]),

            // Tiempos de resolución
            Section::make('Tiempos de Resolución (minutos)')
                ->schema([
                    Grid::make(4)
                        ->schema([
                            TextInput::make('tiempo_resolucion_critico')
                                ->label('Crítico')
                                ->numeric()
                                ->required()
                                ->suffix('min'),
                            TextInput::make('tiempo_resolucion_alto')
                                ->label('Alto')
                                ->numeric()
                                ->required()
                                ->suffix('min'),
                            TextInput::make('tiempo_resolucion_medio')
                                ->label('Medio')
                                ->numeric()
                                ->required()
                                ->suffix('min'),
                            TextInput::make('tiempo_resolucion_bajo')
                                ->label('Bajo')
                                ->numeric()
                                ->required()
                                ->suffix('min'),
                        ]),
                ]),

            // Configuración de escalamiento
            Section::make('Escalamiento')
                ->schema([
                    Toggle::make('escalamiento_automatico')
                        ->label('Escalamiento Automático')
                        ->reactive(),

                    TextInput::make('factor_escalamiento')
                        ->label('Factor de Escalamiento')
                        ->numeric()
                        ->step(0.1)
                        ->visible(fn($get) => $get('escalamiento_automatico'))
                        ->helperText('Multiplicador para los tiempos (ej: 1.5 = 150%)'),
                ]),

            Toggle::make('activo')
                ->label('SLA Activo')
                ->default(true),
        ]);
}
```

### 5. DispositivoResource

**Archivo:** `app/Filament/Resources/DispositivoResource.php`

#### Descripción
Gestión completa del inventario de dispositivos.

#### Formulario
```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('Información General')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('nombre')
                                ->label('Nombre')
                                ->required()
                                ->maxLength(100),

                            TextInput::make('numero_serie')
                                ->label('Número de Serie')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(50),

                            TextInput::make('marca')
                                ->label('Marca')
                                ->maxLength(50),

                            TextInput::make('modelo')
                                ->label('Modelo')
                                ->maxLength(50),

                            Select::make('categoria_dispositivo_id')
                                ->label('Categoría')
                                ->relationship('categoriaDispositivo', 'nombre')
                                ->required(),

                            Select::make('area_id')
                                ->label('Área')
                                ->relationship('area', 'nombre')
                                ->required(),

                            Select::make('locale_id')
                                ->label('Ubicación')
                                ->relationship('locale', 'nombre')
                                ->required(),

                            Select::make('estado')
                                ->label('Estado')
                                ->options([
                                    'Activo' => 'Activo',
                                    'Inactivo' => 'Inactivo',
                                    'En Reparación' => 'En Reparación',
                                    'Dado de Baja' => 'Dado de Baja',
                                ])
                                ->required(),
                        ]),
                ]),

            Section::make('Información Comercial')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            DatePicker::make('fecha_adquisicion')
                                ->label('Fecha de Adquisición'),

                            TextInput::make('valor_adquisicion')
                                ->label('Valor de Adquisición')
                                ->numeric()
                                ->prefix('$'),

                            TextInput::make('proveedor')
                                ->label('Proveedor')
                                ->maxLength(100),

                            TextInput::make('garantia_meses')
                                ->label('Garantía (meses)')
                                ->numeric()
                                ->suffix('meses'),
                        ]),
                ]),

            Section::make('Observaciones')
                ->schema([
                    Textarea::make('descripcion')
                        ->label('Descripción')
                        ->rows(3),

                    Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(3),
                ]),
        ]);
}
```

---

## 📊 Widgets

### 1. TicketsStatsWidget

**Archivo:** `app/Filament/Widgets/TicketsStatsWidget.php`

#### Descripción
Widget que muestra estadísticas principales de tickets.

```php
protected function getStats(): array
{
    return [
        Stat::make('Total Tickets', Ticket::count())
            ->description('Todos los tickets')
            ->descriptionIcon('heroicon-m-ticket')
            ->color('primary'),

        Stat::make('Abiertos', Ticket::where('estado', 'Abierto')->count())
            ->description('Tickets abiertos')
            ->descriptionIcon('heroicon-m-clock')
            ->color('danger'),

        Stat::make('En Progreso', Ticket::where('estado', 'En Progreso')->count())
            ->description('En proceso')
            ->descriptionIcon('heroicon-m-cog')
            ->color('warning'),

        Stat::make('Cerrados', Ticket::where('estado', 'Cerrado')->count())
            ->description('Tickets cerrados')
            ->descriptionIcon('heroicon-m-check-circle')
            ->color('success'),

        Stat::make('Escalados', Ticket::where('escalado', true)->count())
            ->description('Tickets escalados')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('danger'),

        Stat::make('SLA Vencido', Ticket::where('sla_vencido', true)->count())
            ->description('SLA vencido')
            ->descriptionIcon('heroicon-m-exclamation-triangle')
            ->color('danger'),
    ];
}
```

### 2. TicketsPorAreaWidget

**Archivo:** `app/Filament/Widgets/TicketsPorAreaWidget.php`

#### Descripción
Gráfico que muestra la distribución de tickets por área.

```php
protected function getData(): array
{
    $ticketsPorArea = Ticket::select('area_id', DB::raw('count(*) as total'))
        ->join('areas', 'tickets.area_id', '=', 'areas.id')
        ->groupBy('area_id')
        ->with('area')
        ->get();

    return [
        'datasets' => [
            [
                'label' => 'Tickets por Área',
                'data' => $ticketsPorArea->pluck('total')->toArray(),
                'backgroundColor' => [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40',
                ],
            ],
        ],
        'labels' => $ticketsPorArea->pluck('area.nombre')->toArray(),
    ];
}
```

### 3. TicketsPorPrioridadWidget

**Archivo:** `app/Filament/Widgets/TicketsPorPrioridadWidget.php`

#### Descripción
Gráfico de dona que muestra tickets por prioridad.

```php
protected function getData(): array
{
    $ticketsPorPrioridad = Ticket::select('prioridad', DB::raw('count(*) as total'))
        ->groupBy('prioridad')
        ->get();

    return [
        'datasets' => [
            [
                'data' => $ticketsPorPrioridad->pluck('total')->toArray(),
                'backgroundColor' => [
                    '#EF4444', // Crítica - Rojo
                    '#F59E0B', // Alta - Amarillo
                    '#10B981', // Media - Verde
                    '#6B7280', // Baja - Gris
                ],
            ],
        ],
        'labels' => $ticketsPorPrioridad->pluck('prioridad')->toArray(),
    ];
}
```

### 4. TicketsChartWidget

**Archivo:** `app/Filament/Widgets/TicketsChartWidget.php`

#### Descripción
Gráfico temporal que muestra la evolución de tickets.

```php
protected function getData(): array
{
    $ticketsPorDia = Ticket::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('count(*) as total')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('fecha')
        ->orderBy('fecha')
        ->get();

    return [
        'datasets' => [
            [
                'label' => 'Tickets por Día',
                'data' => $ticketsPorDia->pluck('total')->toArray(),
                'borderColor' => '#10B981',
                'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                'fill' => true,
            ],
        ],
        'labels' => $ticketsPorDia->pluck('fecha')->map(function ($fecha) {
            return Carbon::parse($fecha)->format('d/m');
        })->toArray(),
    ];
}
```

---

## 📄 Páginas Personalizadas

### 1. Dashboard

**Archivo:** `app/Filament/Pages/Dashboard.php`

#### Descripción
Dashboard principal con métricas y gráficos.

```php
protected function getWidgets(): array
{
    return [
        TicketsStatsWidget::class,
        TicketsPorAreaWidget::class,
        TicketsPorPrioridadWidget::class,
        TicketsChartWidget::class,
    ];
}

protected function getColumns(): int | array
{
    return [
        'md' => 2,
        'xl' => 4,
    ];
}
```

### 2. Reportes

**Archivo:** `app/Filament/Pages/Reportes.php`

#### Descripción
Página personalizada para generar reportes.

```php
public function render()
{
    return view('filament.pages.reportes', [
        'ticketsStats' => $this->getTicketsStats(),
        'slaStats' => $this->getSlaStats(),
        'areaStats' => $this->getAreaStats(),
    ]);
}

private function getTicketsStats()
{
    return [
        'total' => Ticket::count(),
        'abiertos' => Ticket::where('estado', 'Abierto')->count(),
        'cerrados' => Ticket::where('estado', 'Cerrado')->count(),
        'escalados' => Ticket::where('escalado', true)->count(),
        'sla_vencido' => Ticket::where('sla_vencido', true)->count(),
    ];
}
```

---

## 🔐 Autenticación y Autorización

### Configuración de Roles

```php
// En el seeder
$roles = [
    'Super Admin' => ['*'],
    'Admin' => [
        'ver_tickets',
        'crear_tickets',
        'editar_tickets',
        'eliminar_tickets',
        'gestionar_usuarios',
        'gestionar_areas',
        'gestionar_slas',
    ],
    'Tecnico' => [
        'ver_tickets',
        'editar_tickets',
        'comentar_tickets',
    ],
    'Usuario' => [
        'crear_tickets',
        'ver_propios_tickets',
    ],
];
```

### Políticas de Acceso

```php
// En los recursos
protected static function canAccess(): bool
{
    return auth()->user()->hasAnyRole(['Super Admin', 'Admin']);
}

// En las queries
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    
    if (auth()->user()->hasRole('Super Admin')) {
        return $query;
    }
    
    if (auth()->user()->hasRole('Admin')) {
        return $query->where('area_id', auth()->user()->area_id);
    }
    
    return $query->where('asignado_a', auth()->id());
}
```

---

## 🎨 Personalización

### Tema Personalizado

```php
// En el AppServiceProvider
public function boot()
{
    Filament::serving(function () {
        Filament::registerTheme(
            app(AssetManager::class)->getThemeStylesheetUrl()
        );
    });
}
```

### Iconos Personalizados

```php
// En los recursos
protected static ?string $navigationIcon = 'heroicon-o-ticket';
protected static ?string $activeNavigationIcon = 'heroicon-s-ticket';
```

### Colores Personalizados

```css
/* En resources/css/filament.css */
:root {
    --primary: 16 185 129; /* Verde */
    --danger: 239 68 68;   /* Rojo */
    --warning: 245 158 11; /* Amarillo */
    --success: 34 197 94;  /* Verde éxito */
    --info: 59 130 246;    /* Azul */
}
```

---

## 📋 Mejores Prácticas Implementadas

### 1. **Organización de Código**
- Recursos separados por funcionalidad
- Widgets reutilizables
- Páginas personalizadas para funcionalidades específicas

### 2. **Experiencia de Usuario**
- Navegación intuitiva con badges
- Filtros avanzados
- Acciones contextuales
- Confirmaciones para acciones críticas

### 3. **Rendimiento**
- Lazy loading de relaciones
- Consultas optimizadas
- Cacheo de datos frecuentes

### 4. **Seguridad**
- Autorización granular
- Validación de formularios
- Sanitización de datos

### 5. **Mantenibilidad**
- Código bien documentado
- Convenciones consistentes
- Separación de responsabilidades

---

Esta documentación cubre la implementación completa del panel administrativo Filament. Para detalles específicos de implementación, consulta el código fuente de cada componente.
